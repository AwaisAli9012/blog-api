<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ post: Object, auth: Object })

const isEditing = ref(false)
const editTitle = ref('')
const editContent = ref('')

function startEdit() {
  editTitle.value = props.post.title
  editContent.value = props.post.content
  isEditing.value = true
}
function cancelEdit() {
  isEditing.value = false
  editTitle.value = ''
  editContent.value = ''
}
function saveEdit() {
  if (!editTitle.value.trim() || !editContent.value.trim()) return
  router.put(`/posts/${props.post.id}`, { title: editTitle.value, content: editContent.value }, {
    onSuccess: () => cancelEdit()
  })
}
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
function logout() { router.post('/logout') }
function remove(id) {
  askConfirm('Delete this post? This cannot be undone.', () => router.delete(`/posts/${id}`))
}
</script>

<template>
  <div class="page">

    <div v-if="showModal" class="modal-overlay" @click.self="confirmNo">
      <div class="modal">
        <p class="modal-msg">{{ modalMessage }}</p>
        <div class="modal-actions">
          <button @click="confirmNo" class="modal-cancel">Cancel</button>
          <button @click="confirmYes" class="modal-confirm">Delete</button>
        </div>
      </div>
    </div>
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <template v-if="auth">
          <span class="nav-greeting">Hi, {{ auth.name }}</span>
          <a href="/dashboard" class="nav-link">Dashboard</a>
          <button @click="logout" class="nav-btn">Logout</button>
        </template>
        <template v-else>
          <a href="/login" class="nav-link">Login</a>
          <a href="/register" class="nav-btn">Register</a>
        </template>
      </div>
    </nav>

    <div class="container">
      <a href="/posts" class="back-link">← Back to all posts</a>

      <div class="card">
        <h1 class="post-title">{{ post.title }}</h1>

        <div class="meta">
          <span class="meta-tag">✍️ {{ post.user ? post.user.name : 'Unknown' }}</span>
          <span class="meta-tag">📅 {{ new Date(post.created_at).toLocaleDateString() }}</span>
        </div>

        <div class="post-content">{{ post.content }}</div>

        <div v-if="!isEditing">
          <div v-if="auth && auth.id === post.user_id" class="delete-row">
            <button @click="startEdit()" class="edit-btn">✏️ Edit Post</button>
            <button @click="remove(post.id)" class="delete-btn">🗑 Delete Post</button>
          </div>
        </div>
        <div v-else>
          <div class="field-group">
            <label class="edit-label">Title</label>
            <input v-model="editTitle" class="edit-field" />
          </div>
          <div class="field-group">
            <label class="edit-label">Content</label>
            <textarea v-model="editContent" class="edit-field edit-textarea"></textarea>
          </div>
          <div class="edit-actions">
            <button @click="saveEdit()" class="save-btn">Save Changes</button>
            <button @click="cancelEdit()" class="cancel-btn">Cancel</button>
          </div>
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
.container { max-width: 700px; margin: 0 auto; padding: 48px 20px; }
.back-link { color: #818cf8; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-block; margin-bottom: 24px; }
.back-link:hover { color: #a5b4fc; }
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 36px; }
.post-title { font-size: 1.8rem; font-weight: 800; color: #f1f5f9; line-height: 1.3; margin-bottom: 20px; }
.meta { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #334155; }
.meta-tag { font-size: 0.8rem; color: #94a3b8; background: #0f172a; padding: 4px 12px; border-radius: 20px; border: 1px solid #334155; }
.post-content { color: #94a3b8; font-size: 1rem; line-height: 1.8; white-space: pre-wrap; }
.delete-row { margin-top: 36px; padding-top: 24px; border-top: 1px solid #334155; }
.edit-btn { background: transparent; color: #818cf8; border: 1px solid #818cf8; border-radius: 8px; padding: 8px 20px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
.edit-btn:hover { background: #818cf8; color: white; }
.delete-btn { background: transparent; color: #ef4444; border: 1px solid #ef4444; border-radius: 8px; padding: 8px 20px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
.field-group { margin-bottom: 16px; margin-top: 24px; }
.edit-label { display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
.edit-field { width: 100%; background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; font-family: inherit; outline: none; }
.edit-field:focus { border-color: #6366f1; }
.edit-textarea { min-height: 180px; resize: vertical; line-height: 1.7; }
.edit-actions { display: flex; gap: 12px; margin-top: 20px; }
.save-btn { background: #6366f1; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; }
.save-btn:hover { background: #4f46e5; }
.cancel-btn { background: transparent; color: #64748b; border: 1px solid #334155; padding: 10px 20px; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; }
.cancel-btn:hover { background: #334155; color: #f1f5f9; }
.delete-btn:hover { background: #ef4444; color: white; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; max-width: 360px; width: 90%; text-align: center; }
.modal-msg { color: #f1f5f9; font-size: 1rem; font-weight: 600; margin-bottom: 24px; line-height: 1.5; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.modal-cancel { background: #334155; color: #94a3b8; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
.modal-confirm { background: #ef4444; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
</style>
