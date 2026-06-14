<script setup>
useHead({ title: 'Список продуктів' })

const subscriptionStore = useSubscriptionStore()
const router = useRouter()
const route = useRoute()

const isSuccess = computed(() => route.query.success === '1')

const { data: plans, pending, error } = await useFetch('/api/subscription/plans')

function choosePlan(plan) {
  subscriptionStore.selectPlan(plan)
  router.push('/checkout')
}
</script>

<template>
  <div class="products-page">
    <div class="products-header">
      <h1 class="products-title">Start Your 3 Day Free Trial</h1>
      <p class="products-subtitle">Start your 3-day free trial. Cancel anytime.</p>
    </div>

    <div v-if="isSuccess" class="success-banner">
      Підписка оформлена успішно!
    </div>

    <div v-if="pending" class="loading">Завантаження...</div>
    <div v-else-if="error" class="error-msg">Помилка завантаження планів.</div>

    <div v-else class="plans-grid">
      <div
        v-for="plan in plans"
        :key="plan.id"
        class="plan-card"
        :class="{ 'plan-card--selected': subscriptionStore.selectedPlan?.id === plan.id }"
      >
        <div class="plan-accent-bar"></div>
        <h2 class="plan-name">{{ plan.name }}</h2>
        <p class="plan-trial-label">3-days free then:</p>
        <div class="plan-price">
          <span class="price-amount">${{ plan.monthlyPrice }}</span>
          <span class="price-period">/month</span>
        </div>
        <p class="plan-billing">
          billed yearly at
          <span class="price-original">${{ plan.originalYearly }}</span>
          ${{ plan.yearlyPrice }}
        </p>
        <div class="savings-badge">${{ plan.savings }} in savings</div>

        <button class="choose-btn" @click="choosePlan(plan)">
          Try It Free
        </button>

        <ul class="features-list">
          <li v-for="feature in plan.features" :key="feature.text" class="feature-item">
            <span class="feature-icon">✦</span>
            <span>
              {{ feature.text }}
              <span v-if="feature.sub" class="feature-sub">({{ feature.sub }})</span>
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.products-page {
  min-height: 100vh;
  background: #f7f8fa;
  padding: 48px 24px;
  font-family: 'Helvetica Neue', sans-serif;
}

.products-header {
  text-align: center;
  margin-bottom: 40px;
}

.products-title {
  font-size: 2rem;
  font-weight: 800;
  color: #111;
  margin-bottom: 8px;
}

.products-subtitle {
  color: #666;
  font-size: 1rem;
}

.success-banner {
  max-width: 1000px;
  margin: 0 auto 32px;
  background: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #a5d6a7;
  border-radius: 10px;
  padding: 16px 24px;
  font-size: 1rem;
  font-weight: 600;
  text-align: center;
}

.loading {
  text-align: center;
  color: #888;
  font-size: 1rem;
  padding: 40px;
}

.error-msg {
  text-align: center;
  color: #c62828;
  font-size: 1rem;
  padding: 40px;
}

.plans-grid {
  display: flex;
  gap: 24px;
  justify-content: center;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 0 auto;
}

.plan-card {
  background: #fff;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 28px 24px;
  width: 320px;
  position: relative;
  overflow: hidden;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.plan-card--selected {
  border-color: #4caf50;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
}

.plan-accent-bar {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #4caf50, #26a69a);
}

.plan-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: #111;
  margin: 4px 0 12px;
}

.plan-trial-label {
  font-size: 0.8rem;
  color: #777;
  margin-bottom: 4px;
}

.plan-price {
  display: flex;
  align-items: baseline;
  gap: 2px;
  margin-bottom: 6px;
}

.price-amount {
  font-size: 2.4rem;
  font-weight: 800;
  color: #111;
  line-height: 1;
}

.price-period {
  font-size: 0.9rem;
  color: #555;
}

.plan-billing {
  font-size: 0.8rem;
  color: #777;
  margin-bottom: 10px;
}

.price-original {
  text-decoration: line-through;
  color: #aaa;
}

.savings-badge {
  display: inline-block;
  background: #e8f5e9;
  color: #2e7d32;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
  margin-bottom: 18px;
}

.choose-btn {
  width: 100%;
  background: #f5a623;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 13px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  margin-bottom: 20px;
}

.choose-btn:hover {
  background: #e09412;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: #333;
  line-height: 1.4;
}

.feature-icon {
  color: #4caf50;
  font-size: 0.7rem;
  margin-top: 3px;
  flex-shrink: 0;
}

.feature-sub {
  color: #888;
  font-size: 0.78rem;
}
</style>
