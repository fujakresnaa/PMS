const Meta = {
  name: 'Project Members',
  icon: 'stop_circle',
  module: 'project-members',
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
    user_id: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
