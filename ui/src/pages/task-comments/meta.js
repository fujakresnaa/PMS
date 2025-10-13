const Meta = {
  name: 'Task Comments',
  icon: 'stop_circle',
  module: 'task-comments',
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
    description: null,
    reply: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
