const Meta = {
  name: 'Project Config',
  icon: 'stop_circle',
  module: 'project-config',
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
    value: null,
    description: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
