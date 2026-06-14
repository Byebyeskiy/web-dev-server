<template>
  <div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Редагування категорії</h1>

    <UForm :schema="schema" :state="state" @submit="onSubmit">
      <UFormGroup label="Назва" name="title" class="mb-4">
        <UInput v-model="state.title" placeholder="Введіть назву" />
      </UFormGroup>

      <UFormGroup label="Slug" name="slug" class="mb-4">
        <UInput v-model="state.slug" placeholder="slug-kategorii" />
      </UFormGroup>

      <div class="flex gap-3">
        <UButton type="submit" label="Зберегти" :loading="saving" />
        <UButton label="Скасувати" variant="ghost" to="/admin/categories" />
      </div>
    </UForm>
  </div>
</template>

<script setup lang="ts">
import { z } from 'zod'

const route = useRoute()
const schema = z.object({
  title: z.string().min(2, 'Мінімум 2 символи'),
  slug: z.string().optional()
})

type Schema = z.output<typeof schema>

const state = reactive({ title: '', slug: '' })
const saving = ref(false)

const getCategory = async () => {
  const response: any = await $fetch(`http://127.0.0.1:8000/api/blog/admin/categories/${route.params.id}`)
  state.title = response.data.title
  state.slug = response.data.slug ?? ''
}

const onSubmit = async (event: { data: Schema }) => {
  saving.value = true
  try {
    await $fetch(`http://127.0.0.1:8000/api/blog/admin/categories/${route.params.id}`, {
      method: 'PUT',
      body: event.data
    })
    await navigateTo('/admin/categories')
  } catch (err) {
    console.error(err)
  } finally {
    saving.value = false
  }
}

getCategory()
</script>
