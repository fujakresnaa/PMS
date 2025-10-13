const Meta = {
  name: 'Task Assignees',
  icon: 'stop_circle',
  module: 'task-assignees',
  topBarMenu: [],
  permission: {
    browse: true,
    create: true,
    update: true,
    delete: true,
    restore: true
  },
  model: {
    id: null,
    task_id: null,
    user_id: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
