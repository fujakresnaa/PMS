const Meta = {
  name: 'Projects',
  icon: 'stop_circle',
  module: 'projects',
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
    project_id: null, // shadow
    code: null,
    name: null,
    start_date: null,
    end_date: null,
    actual_start_date: null,
    actual_end_date: null,
    created_by: null,
    updated_by: null,
    deleted_by: null,
    members: null

  }
}

export default Meta
