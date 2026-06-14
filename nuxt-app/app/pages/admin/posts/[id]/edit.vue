<template>
  <div class="p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Редагування посту</h1>
    <UForm :schema="schema" :state="state" @submit="onSubmit">
      <UFormField label="Заголовок" name="title" class="mb-4">
        <UInput v-model="state.title" placeholder="Введіть заголовок" />
      </UFormField>
      <UFormField label="Slug" name="slug" class="mb-4">
        <UInput v-model="state.slug" placeholder="slug-posta" />
      </UFormField>
      <UFormField label="Категорія" name="category_id" class="mb-4">
        <USelect v-model="state.category_id" :options="categoryOptions" placeholder="Оберіть категорію" />
      </UFormField>
      <UFormField label="Короткий опис" name="excerpt" class="mb-4">
        <UTextarea v-model="state.excerpt" placeholder="Короткий опис..." />
      </UFormField>
      <UFormField label="Текст" name="content_raw" class="mb-4">
        <UTextarea v-model="state.content_raw" placeholder="Текст посту..." :rows="8" />
      </UFormField>
      <UFormField name="is_published" class="mb-4">
        <UCheckbox v-model="state.is_published" label="Опубліковано" />
      </UFormField>
      <div class="flex gap-3">
        <UButton type="submit" label="Зберегти" :loading="saving" />
        <UButton label="Скасувати" variant="ghost" to="/admin/posts" />
      </div>
    </UForm>
  </div>
</template>

<script setup lang="ts">
import { z } from 'zod'

const route = useRoute()

const schema = z.object({
  title: z.string().min(3, 'Мінімум 3 символи'),
  slug: z.string().optional(),
  category_id: z.number({ invalid_type_error: 'Оберіть категорію' }),
  excerpt: z.string().optional(),
  content_raw: z.string().min(10, 'Мінімум 10 символів'),
  is_published: z.boolean().optional()
})

type Schema = z.output<typeof schema>

const state = reactive({
  title: '',
  slug: '',
  category_id: undefined as number | undefined,
  excerpt: '',
  content_raw: '',
  is_published: false
})

const saving = ref(false)
const categoryOptions = ref<{ label: string, value: number }[]>([])

const getCategories = async () => {
  const response: any = await $fetch('http://127.0.0.1:8000/api/blog/admin/categories')
  categoryOptions.value = response.data.map((c: any) => ({
    label: c.title,
    value: c.id
  }))
}

const getPost = async () => {
  const response: any = await $fetch(`http://127.0.0.1:8000/api/blog/posts/${route.params.id}`)
  const post = response.data
  state.title = post.title
  state.slug = post.slug ?? ''
  state.category_id = post.category_id
  state.excerpt = post.excerpt ?? ''
  state.content_raw = post.content_raw ?? ''
  state.is_published = post.is_published ?? false
}

const onSubmit = async (event: { data: Schema }) => {
  saving.value = true
  try {
    await $fetch(`http://127.0.0.1:8000/api/blog/posts/${route.params.id}`, {
      method: 'PUT',
      body: event.data
    })
    await navigateTo('/admin/posts')
  } catch (err) {
    console.error(err)
  } finally {
    saving.value = false
  }
}

getCategories()
getPost()
</script>
