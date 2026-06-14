import { defineStore } from 'pinia'

interface PlanFeature {
  text: string
  sub?: string
}

interface Plan {
  id: string
  name: string
  monthlyPrice: string
  yearlyPrice: string
  originalYearly: string
  savings: string
  features: PlanFeature[]
}

export const useSubscriptionStore = defineStore('subscription', () => {
  const selectedPlan = ref<Plan | null>(null)

  const hasPlan = computed(() => selectedPlan.value !== null)

  const monthlyCost = computed(() => {
    if (!selectedPlan.value) return 0
    return selectedPlan.value.monthlyPrice
  })

  function selectPlan(plan: Plan) {
    selectedPlan.value = plan
  }

  function clearPlan() {
    selectedPlan.value = null
  }

  return { selectedPlan, hasPlan, monthlyCost, selectPlan, clearPlan }
})
