
const routes = [
  {
    name: 'login',
    path: '/login',
    component: () => import('layouts/Login.vue')
  },
  {
    path: '/401',
    component: () => import('layouts/NoHeader.vue'),
    children: [
      { name: '401', path: '/401', component: () => import('pages/401.vue') }
    ]
  },
  {
    path: '/403',
    component: () => import('layouts/NoHeader.vue'),
    children: [
      { name: '403', path: '/403', component: () => import('pages/403.vue') }
    ]
  },
  {
    path: '/projects/:project_id/board',
    component: () => import('layouts/KanbanLayout.vue'),
    children: [
      { name: 'board', path: '/projects/:project_id/board', component: () => import('pages/apps/board3.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/Main.vue'),
    children: [
      { name: 'home', path: '/', component: () => import('pages/Index.vue') },

      { name: 'example', path: '/example', component: () => import('pages/example.vue') },
      { name: 'users', path: '/users', component: () => import('pages/users/index.vue') },
      { name: 'view-users', path: '/users/view/:id', component: () => import('pages/users/detail.vue') },
      { name: 'add-users', path: '/users/form', component: () => import('pages/users/form.vue') },
      { name: 'edit-users', path: '/users/form/:id', component: () => import('pages/users/form.vue') },

      { name: 'user-sessions', path: '/user-sessions', component: () => import('pages/user-sessions/index.vue') },
      { name: 'view-user-sessions', path: '/user-sessions/view/:id', component: () => import('pages/user-sessions/detail.vue') },
      { name: 'add-user-sessions', path: '/user-sessions/form', component: () => import('pages/user-sessions/form.vue') },
      { name: 'edit-user-sessions', path: '/user-sessions/form/:id', component: () => import('pages/user-sessions/form.vue') },

      { name: 'permissions', path: '/permissions', component: () => import('pages/permissions/index.vue') },
      { name: 'view-permissions', path: '/permissions/view/:id', component: () => import('pages/permissions/detail.vue') },
      { name: 'add-permissions', path: '/permissions/form', component: () => import('pages/permissions/form.vue') },
      { name: 'edit-permissions', path: '/permissions/form/:id', component: () => import('pages/permissions/form.vue') },

      { name: 'roles', path: '/roles', component: () => import('pages/roles/index.vue') },
      { name: 'view-roles', path: '/roles/view/:id', component: () => import('pages/roles/detail.vue') },
      { name: 'add-roles', path: '/roles/form', component: () => import('pages/roles/form.vue') },
      { name: 'edit-roles', path: '/roles/form/:id', component: () => import('pages/roles/form.vue') },

      { name: 'user-notifications', path: '/user-notifications', component: () => import('pages/user-notifications/index.vue') },
      { name: 'view-user-notifications', path: '/user-notifications/view/:id', component: () => import('pages/user-notifications/detail.vue') },
      { name: 'add-user-notifications', path: '/user-notifications/form', component: () => import('pages/user-notifications/form.vue') },
      { name: 'edit-user-notifications', path: '/user-notifications/form/:id', component: () => import('pages/user-notifications/form.vue') },
      { name: 'notifications', path: '/notifications', component: () => import('pages/user-notifications/user-list.vue') },

      { name: 'update-profile-users', path: '/users/update-profile', component: () => import('pages/users/update-profile.vue') },
      { name: 'change-password-users', path: '/users/change-password', component: () => import('pages/users/change-password.vue') },

      // modules
      { name: 'projects', path: '/projects', component: () => import('pages/projects/index.vue') },
      { name: 'projects-all', path: '/projects/show/:all', component: () => import('pages/projects/index.vue') },
      { name: 'view-projects', path: '/projects/view/:id', component: () => import('pages/projects/detail.vue') },
      { name: 'add-projects', path: '/projects/form', component: () => import('pages/projects/form.vue') },
      { name: 'edit-projects', path: '/projects/form/:id', component: () => import('pages/projects/form.vue') },

      // lingkup project

      { name: 'roadmaps', path: '/projects/:project_id/roadmaps', component: () => import('pages/apps/roadmaps.vue') },
      { name: 'erd', path: '/projects/:project_id/erd', component: () => import('pages/apps/erd.vue') },
      // { name: 'timeline', path: '/projects/:project_id/timeline', component: () => import('pages/apps/timeline.vue') },
      { name: 'timeline', path: '/projects/:project_id/timeline', component: () => import('pages/apps/gantt.vue') },
      // { name: 'board', path: '/projects/:project_id/board', component: () => import('pages/apps/board.vue') },
      // { name: 'board', path: '/projects/:project_id/board', component: () => import('pages/apps/board2.vue') },
      // { name: 'board3', path: '/projects/:project_id/board3', component: () => import('pages/apps/board3.vue') },
      // { name: 'timeline', path: '/projects/:project_id/timeline', component: () => import('pages/works/index.vue') },
      { name: 'components', path: '/projects/:project_id/components', component: () => import('pages/apps/components.vue') },

      // default module
      // { name: 'works', path: '/works/:project_id', component: () => import('pages/works/index.vue') },
      { name: 'project-works', path: '/projects/:project_id/works', component: () => import('pages/works/index.vue') },
      { name: 'view-works', path: '/projects/:project_id/works/view/:id', component: () => import('pages/works/detail.vue') },
      { name: 'add-works', path: '/projects/:project_id/works/form', component: () => import('pages/works/form.vue') },
      { name: 'edit-works', path: '/projects/:project_id/works/form/:id', component: () => import('pages/works/form.vue') },

      { name: 'project-config', path: '/projects/:project_id/config', component: () => import('pages/project-config/index.vue') },
      { name: 'view-project-config', path: '/projects/:project_id/config/view/:id', component: () => import('pages/project-config/detail.vue') },
      { name: 'add-project-config', path: '/projects/:project_id/config/form', component: () => import('pages/project-config/form.vue') },
      { name: 'edit-project-config', path: '/projects/:project_id/config/form/:id', component: () => import('pages/project-config/form.vue') },

      // { name: 'work-labels', path: '/work-labels/:work_id', component: () => import('pages/work-labels/index.vue') },
      { name: 'project-work-labels', path: '/projects/:project_id/work-labels', component: () => import('pages/work-labels/index.vue') },
      { name: 'view-work-labels', path: '/projects/:project_id/work-labels/view/:id', component: () => import('pages/work-labels/detail.vue') },
      { name: 'add-work-labels', path: '/projects/:project_id/work-labels/form', component: () => import('pages/work-labels/form.vue') },
      { name: 'edit-work-labels', path: '/projects/:project_id/work-labels/form/:id', component: () => import('pages/work-labels/form.vue') },

      { name: 'project-sprints', path: '/projects/:project_id/sprints', component: () => import('pages/sprints/index.vue') },
      // { name: 'sprints', path: '/sprints/:work_id', component: () => import('pages/sprints/index.vue') },
      { name: 'view-sprints', path: '/projects/:project_id/sprints/view/:id', component: () => import('pages/sprints/detail.vue') },
      { name: 'add-sprints', path: '/projects/:project_id/sprints/form', component: () => import('pages/sprints/form.vue') },
      { name: 'edit-sprints', path: '/projects/:project_id/sprints/form/:id', component: () => import('pages/sprints/form.vue') },

      { name: 'project-tasks', path: '/projects/:project_id/tasks', component: () => import('pages/tasks/index.vue') },
      // { name: 'tasks', path: '/tasks/:sprint_id', component: () => import('pages/tasks/index.vue') },
      { name: 'view-tasks', path: '/projects/:project_id/tasks/view/:id', component: () => import('pages/tasks/detail.vue') },
      { name: 'add-tasks', path: '/projects/:project_id/tasks/form', component: () => import('pages/tasks/form.vue') },
      { name: 'edit-tasks', path: '/projects/:project_id/tasks/form/:id', component: () => import('pages/tasks/form.vue') },

      { name: 'project-task-status', path: '/projects/:project_id/task-status', component: () => import('pages/task-status/index.vue') },
      // { name: 'task-status', path: '/task-status/:task_id', component: () => import('pages/task-status/index.vue') },
      { name: 'view-task-status', path: '/projects/:project_id/task-status/view/:id', component: () => import('pages/task-status/detail.vue') },
      { name: 'add-task-status', path: '/projects/:project_id/task-status/form', component: () => import('pages/task-status/form.vue') },
      { name: 'edit-task-status', path: '/projects/:project_id/task-status/form/:id', component: () => import('pages/task-status/form.vue') },

      { name: 'project-task-labels', path: '/projects/:project_id/task-labels', component: () => import('pages/task-labels/index.vue') },
      // { name: 'task-labels', path: '/task-labels/:task_id', component: () => import('pages/task-labels/index.vue') },
      { name: 'view-task-labels', path: '/projects/:project_id/task-labels/view/:id', component: () => import('pages/task-labels/detail.vue') },
      { name: 'add-task-labels', path: '/projects/:project_id/task-labels/form', component: () => import('pages/task-labels/form.vue') },
      { name: 'edit-task-labels', path: '/projects/:project_id/task-labels/form/:id', component: () => import('pages/task-labels/form.vue') },

      { name: 'project-task-components', path: '/projects/:project_id/task-components', component: () => import('pages/task-components/index.vue') },
      { name: 'view-task-components', path: '/projects/:project_id/task-components/view/:id', component: () => import('pages/task-components/detail.vue') },
      { name: 'add-task-components', path: '/projects/:project_id/task-components/task/:task_id/form', component: () => import('pages/task-components/form.vue') },
      { name: 'edit-task-components', path: '/projects/:project_id/task-components/task/:task_id/form/:id', component: () => import('pages/task-components/form.vue') }

    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
