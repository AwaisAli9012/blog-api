<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

// Accept posts from the controller
defineProps({
  posts: Array
})

// Form fields
const title = ref('')
const content = ref('')

// Submit function
function submit() {
  router.post('/posts', {
    title: title.value,
    content: content.value
  })
}

// Delete function
function remove(id) {
  router.delete(`/posts/${id}`)
}
</script>

<template>
  <div>
    <h1>All Posts</h1>
    <ul>
      <li v-for="post in posts" :key="post.id">
        <strong>{{ post.title }}</strong> - {{ post.content }}
        <button @click="remove(post.id)">Delete</button>
      </li>
    </ul>

    <h2>Create New Post</h2>
    <form @submit.prevent="submit">
      <input v-model="title" placeholder="Title" />
      <textarea v-model="content" placeholder="Content"></textarea>
      <button type="submit">Add Post</button>
    </form>
  </div>
</template>
