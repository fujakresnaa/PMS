const Meta = {
  name: 'Task Components',
  icon: 'stop_circle',
  module: 'task-components',
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
    task_id: null,
    name: null,
    summary: null,
    description: '',
    type: 'DOC',
    created_by: null,
    updated_by: null,
    deleted_by: null,
    tasks: null

  }
}

export default Meta
