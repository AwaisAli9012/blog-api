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

      <!-- Navbar -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px">
        <a href="/posts" style="color:white;font-size:1.2rem;font-weight:800;text-decoration:none">✍️ My Blog</a>
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

      <!-- Header -->
      <div style="text-align:center;margin-bottom:40px">
        <h1 style="font-size:2.8rem;font-weight:800;color:white;margin:0 0 8px;text-shadow:0 2px 8px rgba(0,0,0,0.2)">✍️ My Blog</h1>
        <p style="color:rgba(255,255,255,0.8);font-size:1.1rem;margin:0">Share your thoughts with the world</p>
      </div>

      <!-- Posts List -->
      <div style="display:flex;flex-direction:column;gap:20px;margin-bottom:40px">
        <div v-for="post in posts" :key="post.id"
          style="background:white;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.12);padding:24px;display:flex;justify-content:space-between;align-items:flex-start;transition:transform 0.2s"
          onmouseover="this.style.transform='translateY(-2px)'"
          onmouseout="this.style.transform='translateY(0)'">
          <div style="flex:1">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
              <div style="width:8px;height:8px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%"></div>
              <a :href="`/posts/${post.id}`" style="font-size:1.2rem;font-weight:700;color:#1a1a2e;text-decoration:none">{{ post.title }}</a>
            </div>
            <p style="color:#6b7280;font-size:0.95rem;margin:0 0 12px;line-height:1.6">{{ post.content }}</p>
            <span style="font-size:0.75rem;color:#a0aec0;background:#f7fafc;padding:3px 10px;border-radius:20px">📅 Just now</span>
            <span v-if="post.user" style="font-size:0.75rem;color:#a0aec0;background:#f7fafc;padding:3px 10px;border-radius:20px;margin-left:6px">✍️ {{ post.user.name }}</span>
          </div>
          <button v-if="auth && auth.id === post.user_id" @click="remove(post.id)"
            style="margin-left:20px;background:#fff0f0;color:#ef4444;border:1px solid #fecaca;border-radius:8px;padding:6px 14px;cursor:pointer;font-weight:600;font-size:0.85rem;transition:background 0.2s"
            onmouseover="this.style.background='#fee2e2'"
            onmouseout="this.style.background='#fff0f0'">
            🗑 Delete
          </button>
        </div>

        <!-- Comments Section -->
        <div style="margin-top:16px;border-top:1px solid #f3f4f6;padding-top:12px">
          <button @click="toggleComments(post.id)"
            style="background:none;border:none;cursor:pointer;color:#667eea;font-size:0.85rem;font-weight:600;padding:0;display:flex;align-items:center;gap:6px">
            💬 {{ (post.comments ?? []).length }} Comment{{ post.comments?.length !== 1 ? 's' : '' }}
            <span style="font-size:0.7rem">{{ openComments[post.id] ? '▲' : '▼' }}</span>
          </button>

          <div v-if="openComments[post.id]" style="margin-top:14px;display:flex;flex-direction:column;gap:12px">

            <!-- Existing comments -->
            <div v-if="post.comments && post.comments.length > 0">
              <div v-for="comment in (post.comments ?? [])" :key="comment.id"
                style="background:#f9fafb;border-radius:10px;padding:12px 16px;border-left:3px solid #667eea">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                  <div style="width:28px;height:28px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:700">
                    {{ comment.user?.name?.charAt(0).toUpperCase() }}
                  </div>
                  <span style="font-weight:600;font-size:0.85rem;color:#1a1a2e">{{ comment.user?.name }}</span>
                </div>
                <p style="margin:0;font-size:0.9rem;color:#4b5563;line-height:1.5">{{ comment.body }}</p>
              </div>
            </div>

            <div v-else style="color:#9ca3af;font-size:0.85rem;text-align:center;padding:8px">
              No comments yet. Be the first!
            </div>

            <!-- Add comment input -->
            <div v-if="auth" style="display:flex;gap:8px;margin-top:4px">
              <input
                v-model="commentText[post.id]"
                placeholder="Write a comment..."
                @keyup.enter="submitComment(post.id)"
                style="flex:1;border:2px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.85rem;outline:none;color:#1f2937"
                onfocus="this.style.borderColor='#667eea'"
                onblur="this.style.borderColor='#e5e7eb'"
              />
              <button @click="submitComment(post.id)"
                style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:8px;padding:8px 16px;font-size:0.85rem;font-weight:600;cursor:pointer">
                Post
              </button>
            </div>

            <p v-else style="font-size:0.8rem;color:#9ca3af;margin:0">
              <a href="/login" style="color:#667eea;font-weight:600">Login</a> to leave a comment
            </p>

          </div>
        </div>

        <div v-if="posts.length === 0"
          style="background:rgba(255,255,255,0.15);border-radius:16px;padding:48px;text-align:center">
          <p style="color:white;font-size:1.1rem;margin:0">No posts yet. Be the first to write one! 🚀</p>
        </div>
      </div>

      <!-- Create Post Form -->
      <div v-if="auth" style="background:white;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.15);padding:32px">
        <h2 style="font-size:1.6rem;font-weight:800;color:#1a1a2e;margin:0 0 6px">Create New Post</h2>
        <p style="color:#9ca3af;margin:0 0 24px;font-size:0.9rem">Fill in the details below to publish your post</p>

        <form @submit.prevent="submit" style="display:flex;flex-direction:column;gap:16px">
          <div>
            <label style="display:block;font-size:0.85rem;font-weight:600;color:#374151;margin-bottom:6px">Post Title</label>
            <input v-model="title" placeholder="Enter an interesting title..."
              style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;transition:border-color 0.2s;color:#1f2937"
              onfocus="this.style.borderColor='#667eea'"
              onblur="this.style.borderColor='#e5e7eb'"/>
          </div>
          <div>
            <label style="display:block;font-size:0.85rem;font-weight:600;color:#374151;margin-bottom:6px">Content</label>
            <textarea v-model="content" placeholder="Write your post content here..." rows="5"
              style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;resize:vertical;color:#1f2937;transition:border-color 0.2s"
              onfocus="this.style.borderColor='#667eea'"
              onblur="this.style.borderColor='#e5e7eb'"></textarea>
          </div>
          <button type="submit"
            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border:none;border-radius:10px;padding:14px 32px;font-size:1rem;font-weight:700;cursor:pointer;align-self:flex-start;box-shadow:0 4px 15px rgba(102,126,234,0.4);transition:opacity 0.2s"
            onmouseover="this.style.opacity='0.9'"
            onmouseout="this.style.opacity='1'">
            🚀 Publish Post
          </button>
        </form>
      </div>

      <!-- Guest prompt -->
      <div v-else style="background:white;border-radius:20px;padding:32px;text-align:center">
        <p style="color:#6b7280;font-size:1rem">Want to share your thoughts?
          <a href="/login" style="color:#667eea;font-weight:700">Login</a> or
          <a href="/register" style="color:#667eea;font-weight:700">Register</a>
        </p>
      </div>

    </div>
  </div>

  <ConfirmModal
    :show="showDeleteModal"
    title="Delete Post"
    message="Are you sure you want to delete this post? This cannot be undone."
    confirmText="Delete"
    confirmColor="#ef4444"
    icon="🗑️"
    @confirm="confirmDelete"
    @cancel="showDeleteModal = false"
  />

  <ConfirmModal
    :show="showLogoutModal"
    title="Log Out"
    message="You can always log back in anytime."
    confirmText="Log Out"
    confirmColor="#f97316"
    icon="🔐"
    @confirm="confirmLogout"
    @cancel="showLogoutModal = false"
  />
</template>