<script setup>
import { ref } from 'vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { router } from '@inertiajs/vue3'

defineProps({ 
    posts: Array,
    auth: Object
})

const title = ref('')
const content = ref('')

const showDeleteModal = ref(false)
const showLogoutModal = ref(false)
const deletePostId = ref(null)
const openComments = ref({})
const commentText = ref({})

function toggleComments(postId) {
  openComments.value = {
    ...openComments.value,
    [postId]: !openComments.value[postId]
  }

  if (!commentText.value[postId]) {
    commentText.value[postId] = ''
  }
}

function deleteComment(commentId) {
  router.delete(`/comments/${commentId}`)
}

function submitComment(postId) {
  if (!commentText.value[postId]?.trim()) return

  router.post(`/posts/${postId}/comments`, 
    { body: commentText.value[postId] }, 
    {
      onSuccess: () => {
        commentText.value[postId] = ''
      }
    }
  )
}

function submit() {
  router.post('/posts', 
    { title: title.value, content: content.value }, 
    {
      onSuccess: () => {
        title.value = ''
        content.value = ''
      }
    }
  )
}

function remove(id) {
  deletePostId.value = id
  showDeleteModal.value = true
}

function confirmDelete() {
  router.delete(`/posts/${deletePostId.value}`, {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}

function logout() {
  showLogoutModal.value = true
}

function confirmLogout() {
  router.post('/logout', {
    onFinish: () => {
      showLogoutModal.value = false
    }
  })
}
</script>

<template>
  <div class="page">
    <div class="container">

      <!-- Navbar -->
      <div class="navbar">
        <a href="/posts" class="logo">✍️ My Blog</a>

        <div class="nav-actions">
          <template v-if="auth && auth.id">
            <span class="user">Hi, {{ auth.name }}</span>
            <a href="/dashboard" class="btn">Dashboard</a>
            <a href="#" class="btn" @click.prevent="logout">Logout</a>
          </template>

          <template v-else>
            <a href="/login" class="btn">Login</a>
            <a href="/register" class="btn">Register</a>
          </template>
        </div>
      </div>

      <!-- Header -->
      <div class="header">
        <h1>✍️ My Blog</h1>
        <p>Share your thoughts with the world</p>
      </div>

      <!-- Posts -->
      <div class="posts">

        <div v-for="post in posts" :key="post.id" class="card">

          <div class="card-content">
            <a :href="`/posts/${post.id}`" class="title">
              {{ post.title }}
            </a>

            <p class="content">{{ post.content }}</p>

            <div class="meta">
              <span>📅 Just now</span>
              <span v-if="post.user">✍️ {{ post.user.name }}</span>
            </div>
          </div>

          <button
            v-if="auth && auth.id === post.user_id"
            @click="remove(post.id)"
            class="delete-btn"
          >
            🗑 Delete
          </button>

          <!-- ✅ COMMENTS INSIDE LOOP -->
          <div class="comments">

            <button @click="toggleComments(post.id)" class="comment-toggle">
              💬 {{ (post.comments ?? []).length }} Comments
              <span>{{ openComments[post.id] ? '▲' : '▼' }}</span>
            </button>

            <div v-if="openComments[post.id]" class="comment-box">

              <!-- Existing comments -->
              <div v-if="(post.comments ?? []).length > 0">
                <div
                  v-for="comment in (post.comments ?? [])"
                  :key="comment.id || comment.created_at"
                  class="comment"
                >
                  <div class="comment-avatar">
                    {{ (comment.user?.name ?? "?").charAt(0).toUpperCase() }}
                  </div>
                  <div class="comment-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <strong>{{ comment.user?.name ?? "Unknown" }}</strong>
                      <button
                        v-if="auth && auth.id === comment.user_id"
                        @click="deleteComment(comment.id)"
                        style="background:none;border:none;cursor:pointer;color:#f87171;font-size:0.8rem;padding:2px 6px;border-radius:6px;line-height:1"
                      >🗑</button>
                    </div>
                    <p>{{ comment.body }}</p>
                  </div>
                </div>
              </div>

              <div v-else class="no-comments">
                No comments yet.
              </div>

              <!-- Add comment -->
              <div v-if="auth && auth.id" class="comment-input">
                <input
                  v-model="commentText[post.id]"
                  placeholder="Write a comment..."
                  
                />
                <button @click="submitComment(post.id)">Post</button>
              </div>

              <p v-else class="login-msg">
                <a href="/login">Login</a> to comment
              </p>

            </div>
          </div>

        </div>

        <div v-if="posts.length === 0" class="empty">
          No posts yet 🚀
        </div>

      </div>

      <!-- Create Post -->
      <div v-if="auth && auth.id" class="create">

        <h2>Create Post</h2>

        <form @submit.prevent="submit">

          <input
            v-model="title"
            placeholder="Title..."
          />

          <textarea
            v-model="content"
            placeholder="Content..."
          ></textarea>

          <button type="submit">Publish</button>

        </form>
      </div>

    </div>
  </div>

  <!-- Modals -->
  <ConfirmModal
    :show="showDeleteModal"
    title="Delete Post"
    message="Are you sure?"
    confirmText="Delete"
    @confirm="confirmDelete"
    @cancel="showDeleteModal = false"
  />

  <ConfirmModal
    :show="showLogoutModal"
    title="Log Out"
    message="You can log back in anytime."
    confirmText="Log Out"
    @confirm="confirmLogout"
    @cancel="showLogoutModal = false"
  />
</template>

<style scoped>
.page {
  min-height: 100vh;
  background: linear-gradient(135deg,#667eea,#764ba2);
  padding: 40px 16px;
}

.container {
  max-width: 760px;
  margin: auto;
}

.navbar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 30px;
}

.logo {
  color: white;
  font-weight: bold;
}

.btn {
  margin-left: 10px;
  color: white;
}

.header {
  text-align: center;
  color: white;
  margin-bottom: 30px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-3px);
}

.title {
  font-weight: bold;
  font-size: 18px;
}

.content {
  margin: 10px 0;
}

.meta {
  font-size: 12px;
  color: gray;
}

.delete-btn {
  color: red;
  margin-top: 10px;
}

.comments {
  margin-top: 20px;
  border-top: 2px solid #f0f0ff;
  padding-top: 16px;
}

.comment-toggle {
  background: linear-gradient(135deg, #f0f0ff, #e8e8ff);
  border: none;
  cursor: pointer;
  color: #667eea;
  font-size: 0.85rem;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.comment-toggle:hover {
  background: linear-gradient(135deg, #e8e8ff, #d8d8ff);
}

.comment-box {
  margin-top: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.comment {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  padding: 14px;
  background: linear-gradient(135deg, #f8f9ff, #f0f0ff);
  border-radius: 14px;
  margin-bottom: 8px;
}

.comment-avatar {
  width: 36px;
  height: 36px;
  min-width: 36px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 800;
  font-size: 0.9rem;
  box-shadow: 0 2px 8px rgba(102,126,234,0.4);
}

.comment-body {
  flex: 1;
}

.comment-body strong {
  font-size: 0.85rem;
  color: #1a1a2e;
  display: block;
  margin-bottom: 4px;
}

.comment-body p {
  margin: 0;
  font-size: 0.9rem;
  color: #4b5563;
  line-height: 1.5;
}

.no-comments {
  text-align: center;
  padding: 20px;
  background: #f8f9ff;
  border-radius: 14px;
  border: 2px dashed #e0e0ff;
  color: #a0aec0;
  font-size: 0.9rem;
}

.comment-input {
  display: flex;
  gap: 10px;
  align-items: center;
  background: #f8f9ff;
  padding: 12px;
  border-radius: 14px;
  border: 2px solid #e8e8ff;
  margin-top: 4px;
}

.comment-input input {
  flex: 1;
  border: none;
  background: white;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.9rem;
  outline: none;
  color: #1f2937;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  width: auto;
}

.comment-input button {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(102,126,234,0.4);
  width: auto;
}

.login-msg {
  text-align: center;
  font-size: 0.85rem;
  color: #9ca3af;
  margin: 0;
}

.login-msg a {
  color: #667eea;
  font-weight: 700;
  text-decoration: none;
}

.create {
  background: white;
  padding: 20px;
  border-radius: 12px;
}

input, textarea {
  width: 100%;
  margin-bottom: 10px;
}
</style>