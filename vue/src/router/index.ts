import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: Dashboard
    },
    {
      path: '/teachers',
      name: 'Teachers',
      component: () => import('../views/Teacher.vue')
    },
    {
      path: '/students',
      name: 'Students',
      component: () => import('../views/Students.vue')
    },
    {
      path: '/classes',
      name: 'Classes',
      component: () => import('../views/Classes.vue')
    },
    {
      path: '/schedule',
      name: 'Schedule',
      component: () => import('../views/Schedule.vue')
    },
    {
      path: '/reports',
      name: 'Reports',
      component: () => import('../views/Reports.vue')
    }
  ]
});

export default router;