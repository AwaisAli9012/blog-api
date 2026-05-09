<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ posts: Array, auth: Object })
const commentText = ref({})
const openComments = ref({})
const title = ref('')
const content = ref('')

// Custom modal state
const showModal = ref(false)
const modalMessage = ref('')
const modalAction = ref(null)

function askConfirm(message, action) {
  modalMessage.value = message
  modalAction.value = action
  showModal.value = true
}
function confirmYes() {
  if (modalAction.value) modalAction.value()
  showModal.value = false
}
function confirmNo() { showModal.value = false }

function toggleComments(id) { openComments.value[id] = !openComments.value[id] }

function deleteComment(id) {
  askConfirm('Delete this comment?', () => router.delete(`/comments/${id}`))
}
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
  askConfirm('Delete this post? This cannot be undone.', () => router.delete(`/posts/${id}`))
}
</script>

<template>
  <div class="page">

    <!-- Custom Confirm Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="confirmNo">
      <div class="modal">
        <p class="modal-msg">{{ modalMessage }}</p>
        <div class="modal-actions">
          <button @click="confirmNo" class="modal-cancel">Cancel</button>
          <button @click="confirmYes" class="modal-confirm">Delete</button>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <template v-if="auth">
          <span class="nav-greeting">Hi, {{ auth.name }}</span>
          <a href="/dashboard" class="nav-link">Dashboard</a>
          <a href="/posts/create" class="nav-btn">+ New Post</a>
          <button @click="router.post('/logout')" class="nav-btn">Logout</button>
        </template>
        <template v-else>
          <a href="/login" class="nav-link">Login</a>
          <a href="/register" class="nav-btn">Register</a>
        </template>
      </div>
    </nav>

    <div class="container">

      <!-- Guest Banner -->
      <div v-if="!auth" class="guest-banner">
        <div>
          <p class="guest-title">👋 Welcome to My Blog</p>
          <p class="guest-sub">Login or create an account to write posts and join the conversation.</p>
        </div>
        <div class="guest-actions">
          <a href="/login" class="guest-login">Login</a>
          <a href="/register" class="guest-register">Register</a>
        </div>
      </div>

      <!-- Posts -->
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
            <button v-if="auth && auth.id === post.user_id" @click="remove(post.id)" class="delete-icon-btn">🗑</button>
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
                      <button v-if="auth && auth.id === comment.user_id" @click="deleteComment(comment.id)" class="delete-icon-btn small">🗑</button>
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
              <p v-else class="login-msg">
                <a href="/login">Login</a> to join the conversation
              </p>
            </div>
          </div>
        </div>
      </div>



    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.page { min-height: 100vh; background: #0f172a; color: #f1f5f9; font-family: system-ui, sans-serif; }

/* MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; max-width: 360px; width: 90%; text-align: center; }
.modal-msg { color: #f1f5f9; font-size: 1rem; font-weight: 600; margin-bottom: 24px; line-height: 1.5; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.modal-cancel { background: #334155; color: #94a3b8; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
.modal-cancel:hover { background: #475569; }
.modal-confirm { background: #ef4444; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
.modal-confirm:hover { background: #dc2626; }

/* NAV */
.nav { background: #1e293b; border-bottom: 1px solid #334155; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
.nav-brand { color: #f1f5f9; font-size: 1.05rem; font-weight: 800; text-decoration: none; }
.nav-links { display: flex; align-items: center; gap: 16px; }
.nav-greeting { color: #64748b; font-size: 0.82rem; }
.nav-link { color: #94a3b8; text-decoration: none; font-size: 0.85rem; }
.nav-link:hover { color: #f1f5f9; }
.nav-btn { background: #6366f1; color: white; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; }

/* GUEST BANNER */
.guest-banner { background: #1e293b; border: 1px solid #334155; border-left: 4px solid #6366f1; border-radius: 14px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
.guest-title { font-size: 1rem; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
.guest-sub { font-size: 0.82rem; color: #64748b; }
.guest-actions { display: flex; gap: 10px; flex-shrink: 0; }
.guest-login { color: #818cf8; text-decoration: none; font-size: 0.85rem; font-weight: 600; padding: 7px 16px; border: 1px solid #6366f1; border-radius: 8px; }
.guest-register { background: #6366f1; color: white; text-decoration: none; font-size: 0.85rem; font-weight: 600; padding: 7px 16px; border-radius: 8px; }

/* LAYOUT */
.container { max-width: 700px; margin: 0 auto; padding: 40px 20px; display: flex; flex-direction: column; gap: 48px; }
.section { display: flex; flex-direction: column; }
.section-label { font-size: 0.72rem; font-weight: 700; color: #475569; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 16px; }
.empty { color: #475569; text-align: center; padding: 48px; }

/* CARD */
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 24px; margin-bottom: 16px; }
.card-top { display: flex; gap: 12px; align-items: flex-start; }
.post-title { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; text-decoration: none; display: block; margin-bottom: 8px; }
.post-title:hover { color: #818cf8; }
.post-content { color: #94a3b8; font-size: 0.88rem; line-height: 1.65; margin-bottom: 12px; }
.post-meta { display: flex; gap: 14px; font-size: 0.75rem; color: #475569; }

/* DELETE BUTTON — now clearly visible */
.delete-icon-btn { background: #2d1515; border: 1px solid #7f1d1d; cursor: pointer; color: #f87171; font-size: 0.95rem; padding: 5px 9px; border-radius: 8px; flex-shrink: 0; transition: all 0.2s; }
.delete-icon-btn:hover { background: #ef4444; color: white; border-color: #ef4444; }
.delete-icon-btn.small { font-size: 0.75rem; padding: 3px 7px; }

/* COMMENTS */
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

/* BUTTONS */
.accent-btn { background: #6366f1; color: white; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.82rem; font-weight: 700; cursor: pointer; white-space: nowrap; }
.accent-btn.large { padding: 11px 24px; font-size: 0.9rem; border-radius: 10px; }
.accent-btn:hover { background: #4f46e5; }

/* CREATE FORM */
.field { width: 100%; background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; outline: none; margin-bottom: 12px; font-family: inherit; }
.field:focus { border-color: #6366f1; }
.field::placeholder { color: #475569; }
.textarea { min-height: 120px; resize: vertical; line-height: 1.6; }
</style>
