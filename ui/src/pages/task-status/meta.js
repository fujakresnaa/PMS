const Meta = {
  name: 'Task Status',
  icon: 'stop_circle',
  module: 'task-status',
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
    project_id: null,
    name: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
