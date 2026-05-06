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
  openComments.value[postId] = !openComments.value[postId]
  if (!commentText.value[postId]) commentText.value[postId] = ''
}

function submitComment(postId) {
  if (!commentText.value[postId]?.trim()) return
  router.post(`/posts/${postId}/comments`, { body: commentText.value[postId] }, {
    onSuccess: () => { commentText.value[postId] = '' }
  })
}

function submit() {
  router.post('/posts', { title: title.value, content: content.value }, {
    onSuccess: () => { title.value = ''; content.value = '' }
  })
}

function remove(id) {
  deletePostId.value = id
  showDeleteModal.value = true
}

function confirmDelete() {
  router.delete(`/posts/${deletePostId.value}`)
  showDeleteModal.value = false
}

function logout() {
  showLogoutModal.value = true
}

function confirmLogout() {
  router.post('/logout')
}
</script>

<template>
  <div style="min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px 16px;font-family:'Segoe UI',sans-serif">
    <div style="max-width:760px;margin:0 auto">

      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px">
        <a href="/posts" style="color:white;font-size:1.2rem;font-weight:800;text-decoration:none">My Blog</a>
        <div style="display:flex;gap:12px">
          <template v-if="auth">
            <span style="color:rgba(255,255,255,0.9);font-size:0.9rem;align-self:center">Hi, {{ auth.name }}</span>
            <a href="/dashboard" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Dashboard</a>
            <a href="#" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600" @click.prevent="logout">Logout</a>
          </template>
          <template v-else>
            <a href="/login" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Login</a>
            <a href="/register" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Register</a>
          </template>
        </div>
      </div>

      <div style="text-align:center;margin-bottom:40px">
        <h1 style="font-size:2.8rem;font-weight:800;color:white;margin:0 0 8px">My Blog</h1>
        <p style="color:rgba(255,255,255,0.8);font-size:1.1rem;margin:0">Share your thoughts with the world</p>
      </div>

      <div style="display:flex;flex-direction:column;gap:20px;margin-bottom:40px">

        <div v-for="post in posts" :key="post.id" style="background:white;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.12);padding:24px">

          <div style="display:flex;justify-content:space-between;align-items:flex-start">
            <div style="flex:1">
              <a :href="`/posts/${post.id}`" style="font-size:1.2rem;font-weight:700;color:#1a1a2e;text-decoration:none;display:block;margin-bottom:8px">{{ post.title }}</a>
              <p style="color:#6b7280;font-size:0.95rem;margin:0 0 12px;line-height:1.6">{{ post.content }}</p>
              <span v-if="post.user" style="font-size:0.75rem;color:#a0aec0;background:#f7fafc;padding:3px 10px;border-radius:20px">by {{ post.user.name }}</span>
            </div>
            <button v-if="auth && auth.id === post.user_id" @click="remove(post.id)"
              style="margin-left:20px;background:#fff0f0;color:#ef4444;border:1px solid #fecaca;border-radius:8px;padding:6px 14px;cursor:pointer;font-weight:600;font-size:0.85rem">
              Delete
            </button>
          </div>

          <div style="margin-top:16px;border-top:1px solid #f3f4f6;padding-top:12px">
            <button @click="toggleComments(post.id)"
              style="background:none;border:none;cursor:pointer;color:#667eea;font-size:0.85rem;font-weight:600;padding:0">
              Comments ({{ (post.comments ?? []).length }})
              {{ openComments[post.id] ? "▲" : "▼" }}
            </button>

            <div v-if="openComments[post.id]" style="margin-top:14px">
              <div v-if="(post.comments ?? []).length > 0">
                <div v-for="comment in (post.comments ?? [])" :key="comment.id"
                  style="background:#f9fafb;border-radius:10px;padding:12px 16px;border-left:3px solid #667eea;margin-bottom:8px">
                  <span style="font-weight:600;font-size:0.85rem;color:#1a1a2e">{{ comment.user?.name }}</span>
                  <p style="margin:4px 0 0;font-size:0.9rem;color:#4b5563">{{ comment.body }}</p>
                </div>
              </div>
              <div v-else style="color:#9ca3af;font-size:0.85rem;padding:8px">
                No comments yet. Be the first!
              </div>

              <div v-if="auth" style="display:flex;gap:8px;margin-top:8px">
                <input v-model="commentText[post.id]" placeholder="Write a comment..." @keyup.enter="submitComment(post.id)"
                  style="flex:1;border:2px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.85rem;outline:none;color:#1f2937" />
                <button @click="submitComment(post.id)"
                  style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:8px;padding:8px 16px;font-size:0.85rem;font-weight:600;cursor:pointer">
                  Post
                </button>
              </div>
              <p v-else style="font-size:0.8rem;color:#9ca3af;margin:8px 0 0">
                <a href="/login" style="color:#667eea;font-weight:600">Login</a> to comment
              </p>
            </div>
          </div>

        </div>

        <div v-if="posts.length === 0" style="background:rgba(255,255,255,0.15);border-radius:16px;padding:48px;text-align:center">
          <p style="color:white;font-size:1.1rem;margin:0">No posts yet. Be the first!</p>
        </div>
      </div>

      <div v-if="auth" style="background:white;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.15);padding:32px">
        <h2 style="font-size:1.6rem;font-weight:800;color:#1a1a2e;margin:0 0 24px">Create New Post</h2>
        <form @submit.prevent="submit" style="display:flex;flex-direction:column;gap:16px">
          <input v-model="title" placeholder="Post title..."
            style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;color:#1f2937" />
          <textarea v-model="content" placeholder="Post content..." rows="5"
            style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;resize:vertical;color:#1f2937"></textarea>
          <button type="submit"
            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border:none;border-radius:10px;padding:14px 32px;font-size:1rem;font-weight:700;cursor:pointer;align-self:flex-start">
            Publish Post
          </button>
        </form>
      </div>

      <div v-else style="background:white;border-radius:20px;padding:32px;text-align:center">
        <p style="color:#6b7280;font-size:1rem">
          <a href="/login" style="color:#667eea;font-weight:700">Login</a> or
          <a href="/register" style="color:#667eea;font-weight:700">Register</a> to post
        </p>
      </div>

    </div>
  </div>

  <ConfirmModal :show="showDeleteModal" title="Delete Post"
    message="Are you sure you want to delete this post?"
    confirmText="Delete" confirmColor="#ef4444" icon="delete"
    @confirm="confirmDelete" @cancel="showDeleteModal = false" />

  <ConfirmModal :show="showLogoutModal" title="Log Out"
    message="You can always log back in anytime."
    confirmText="Log Out" confirmColor="#f97316" icon="logout"
    @confirm="confirmLogout" @cancel="showLogoutModal = false" />
</template>
