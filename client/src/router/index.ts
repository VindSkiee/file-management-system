import { createRouter, createWebHistory } from 'vue-router'
import { clearToken, getToken } from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/departments',
      name: 'departments',
      component: () => import('@/views/DepartmentView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/',
      redirect: { name: 'dashboard' },
    },
  ],
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  if (!authStore.isAuthenticated && getToken()) {
    try {
      await authStore.fetchUser()
    } catch {
      clearToken()
      authStore.user = null
    }
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    return { name: 'dashboard' }
  }
})

export default router
