<template>
  <div class="p-6 max-w-4xl mx-auto">
    <div v-if="loading" class="text-center py-10">
      <p>Завантаження...</p>
    </div>

    <div v-else-if="error" class="text-red-500">
      {{ error }}
    </div>

    <div v-else-if="post">
      <div class="mb-6">
        <a href="/BlogPosts" class="text-blue-400 hover:underline">← Назад до списку</a>
      </div>

      <h1 class="text-3xl font-bold mb-4">{{ post.title }}</h1>

      <div class="flex gap-6 text-sm text-gray-400 mb-6">
        <span>Автор: <strong>{{ post.user?.name }}</strong></span>
        <span>Категорія: <strong>{{ post.category?.title }}</strong></span>
        <span>Дата: <strong>{{ post.published_at }}</strong></span>
      </div>

      <div class="text-gray-300 italic mb-6 border-l-4 border-green-500 pl-4">
        {{ post.excerpt }}
      </div>

      <div class="prose prose-invert max-w-none" v-html="post.content_html" />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Post {
  id: number
  title: string
  excerpt: string
  content_html: string
  published_at: string
  user: { name: string }
  category: { title: string }
}

const route = useRoute()
const post = ref<Post | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const getPost = async () => {
  loading.value = true
  try {
    const response: any = await $fetch(`http://127.0.0.1:8000/api/blog/posts/${route.params.id}`)
    post.value = response
  }
  catch (err: any) {
    error.value = err.message ?? 'Помилка завантаження'
  }
  finally {
    loading.value = false
  }
}

getPost()
</script>
