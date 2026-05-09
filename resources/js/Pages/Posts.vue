<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ posts: Array, auth: Object })
const commentText = ref({})
const openComments = ref({})
const title = ref('')
const content = ref('')

function toggleComments(id) { openComments.value[id] = !openComments.value[id] }
function deleteComment(id) { router.delete(`/comments/${id}`) }
function submitComment(postId) {
  if (!commentText.value[postId]?.trim()) return
  router.post(`/posts/${postId}/comments`, { body: commentText.value[postId] }, {
    onSuccess: () => { commentText.value[postId] = '' }
  })
}
function submit() {
  if (!title.value.trim() || !content.value.trim()) return
  router.post('/posts', { title: title.value, content: content.value }, {
    onSuccess: () => { title.value = ''; content.value = '' }
  })
}
function remove(id) {
  if (confirm('Delete this post?')) router.delete(`/posts/${id}`)
}
</script>

<template>
  <div class="page">
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <template v-if="auth">
          <span class="nav-greeting">Hi, {{ auth.name }}</span>
          <a href="/dashboard" class="nav-link">Dashboard</a>
          <button @click="router.post('/logout')" class="nav-btn">Logout</button>
        </template>
        <template v-else>
          <a href="/login" class="nav-link">Login</a>
          <a href="/register" class="nav-btn">Register</a>
        </template>
      </div>
    </nav>

    <div class="container">
      <div class="section">
        <p class="section-label">LATEST POSTS</p>
        <div v-if="posts.length === 0" class="empty">No posts yet 🚀</div>

        <div v-for="post in posts" :key="post.id" class="card">
          <div class="card-top">
            <div style="flex:1;min-width:0">
              <a :href="`/posts/${post.id}`" class="post-title">{{ post.title }}</a>
              <p class="post-content">{{ post.content }}</p>
              <div class="post-meta">
                <span>📅 Just now</span>
                <span v-if="post.user">✍️ {{ post.user.name }}</span>
              </div>
            </div>
            <button v-if="auth && auth.id === post.user_id" @click="remove(post.id)" class="icon-btn">🗑</button>
          </div>

          <div class="comments">
            <button @click="toggleComments(post.id)" class="comment-toggle">
              💬 {{ (post.comments ?? []).length }} Comment{{ (post.comments ?? []).length !== 1 ? 's' : '' }}
              <span class="arrow">{{ openComments[post.id] ? '▲' : '▼' }}</span>
            </button>

            <div v-if="openComments[post.id]" class="comment-box">
              <div v-if="(post.comments ?? []).length > 0">
                <div v-for="comment in (post.comments ?? [])" :key="comment.id" class="comment">
                  <div class="avatar">{{ (comment.user?.name ?? '?').charAt(0).toUpperCase() }}</div>
                  <div class="comment-body">
                    <div class="comment-row">
                      <strong>{{ comment.user?.name ?? 'Unknown' }}</strong>
                      <button v-if="auth && auth.id === comment.user_id" @click="deleteComment(comment.id)" class="icon-btn small">🗑</button>
                    </div>
                    <p>{{ comment.body }}</p>
                  </div>
                </div>
              </div>
              <div v-else class="no-comments">💭 No comments yet — start the conversation!</div>

              <div v-if="auth" class="comment-input">
                <div class="avatar">{{ (auth.name ?? '?').charAt(0).toUpperCase() }}</div>
                <input v-model="commentText[post.id]" placeholder="Add a comment..." class="comment-field" />
                <button @click="submitComment(post.id)" class="accent-btn">Post ✈️</button>
              </div>
              <p v-else class="login-msg"><a href="/login">Login</a> to join the conversation</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="auth" class="section">
        <p class="section-label">NEW POST</p>
        <div class="card">
          <input v-model="title" placeholder="Title..." class="field" />
          <textarea v-model="content" placeholder="What's on your mind?" class="field textarea"></textarea>
          <button @click="submit" class="accent-btn large">Publish →</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.page { min-height: 100vh; background: #0f172a; color: #f1f5f9; font-family: system-ui, sans-serif; }
.nav { background: #1e293b; border-bottom: 1px solid #334155; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
.nav-brand { color: #f1f5f9; font-size: 1.05rem; font-weight: 800; text-decoration: none; }
.nav-links { display: flex; align-items: center; gap: 16px; }
.nav-greeting { color: #64748b; font-size: 0.82rem; }
.nav-link { color: #94a3b8; text-decoration: none; font-size: 0.85rem; }
.nav-link:hover { color: #f1f5f9; }
.nav-btn { background: #6366f1; color: white; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; }
.container { max-width: 700px; margin: 0 auto; padding: 48px 20px; display: flex; flex-direction: column; gap: 48px; }
.section { display: flex; flex-direction: column; }
.section-label { font-size: 0.72rem; font-weight: 700; color: #475569; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 16px; }
.empty { color: #475569; text-align: center; padding: 48px; }
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 24px; margin-bottom: 16px; }
.card-top { display: flex; gap: 12px; align-items: flex-start; }
.post-title { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; text-decoration: none; display: block; margin-bottom: 8px; }
.post-title:hover { color: #818cf8; }
.post-content { color: #94a3b8; font-size: 0.88rem; line-height: 1.65; margin-bottom: 12px; }
.post-meta { display: flex; gap: 14px; font-size: 0.75rem; color: #475569; }
.icon-btn { background: #2d1f1f; border: 1px solid #4b1f1f; cursor: pointer; color: #f87171; font-size: 1rem; padding: 4px 8px; border-radius: 6px; flex-shrink: 0; }
.icon-btn:hover { color: #ef4444; }
.icon-btn.small { font-size: 0.75rem; }
.comments { margin-top: 20px; border-top: 1px solid #334155; padding-top: 16px; }
.comment-toggle { background: #0f172a; border: 1px solid #334155; cursor: pointer; color: #94a3b8; font-size: 0.8rem; font-weight: 600; padding: 6px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px; }
.comment-toggle:hover { border-color: #6366f1; color: #818cf8; }
.arrow { font-size: 0.6rem; opacity: 0.5; }
.comment-box { margin-top: 14px; display: flex; flex-direction: column; gap: 10px; }
.comment { display: flex; gap: 10px; align-items: flex-start; padding: 12px; background: #0f172a; border-radius: 12px; }
.avatar { width: 32px; height: 32px; min-width: 32px; background: linear-gradient(135deg, #818cf8, #6366f1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.8rem; }
.comment-body { flex: 1; }
.comment-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.comment-body strong { font-size: 0.82rem; color: #e2e8f0; }
.comment-body p { font-size: 0.85rem; color: #94a3b8; line-height: 1.5; }
.no-comments { text-align: center; padding: 16px; background: #0f172a; border-radius: 12px; border: 1px dashed #334155; color: #475569; font-size: 0.85rem; }
.comment-input { display: flex; gap: 10px; align-items: center; background: #0f172a; padding: 10px; border-radius: 12px; border: 1px solid #334155; }
.comment-field { flex: 1; border: none; background: transparent; color: #f1f5f9; font-size: 0.85rem; outline: none; padding: 4px 8px; }
.comment-field::placeholder { color: #475569; }
.login-msg { font-size: 0.8rem; color: #475569; margin-top: 8px; }
.login-msg a { color: #818cf8; font-weight: 700; text-decoration: none; }
.accent-btn { background: #6366f1; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.82rem; font-weight: 700; cursor: pointer; white-space: nowrap; }
.accent-btn.large { padding: 11px 24px; font-size: 0.9rem; border-radius: 10px; }
.accent-btn:hover { background: #4f46e5; }
.field { width: 100%; background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; outline: none; margin-bottom: 12px; font-family: inherit; }
.field:focus { border-color: #6366f1; }
.field::placeholder { color: #475569; }
.textarea { min-height: 120px; resize: vertical; line-height: 1.6; }
</style>
