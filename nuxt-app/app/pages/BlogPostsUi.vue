<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Пости блогу (Nuxt UI)</h1>

    <div class="flex justify-end mb-4">
      <UButton label="Додати" icon="i-lucide-plus" />
    </div>

    <UTable
      :data="posts"
      :columns="columns"
      :loading="loading"
    />

    <div class="flex justify-center mt-4">
      <UPagination
        v-model:page="page"
        :total="total"
        :items-per-page="perPage"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Post {
  id: number
  title: string
  published_at: string | null
  user: { name: string }
  category: { title: string }
}

const posts = ref<Post[]>([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)

const columns = [
  { accessorKey: 'id', header: '#' },
  { accessorKey: 'author_name', header: 'Автор' },
  { accessorKey: 'category_title', header: 'Категорія' },
  { accessorKey: 'title', header: 'Заголовок' },
  { accessorKey: 'date_published', header: 'Дата публікації' }
]

const getPosts = async () => {
  loading.value = true
  try {
    const response: any = await $fetch('http://127.0.0.1:8000/api/blog/posts', {
      params: { page: page.value }
    })
    posts.value = response.data
    total.value = response.meta?.total ?? response.data.length
    perPage.value = response.meta?.per_page ?? 10
  }
  catch (err: any) {
    console.error(err)
  }
  finally {
    loading.value = false
  }
}

watch(page, getPosts)
getPosts()
</script>
