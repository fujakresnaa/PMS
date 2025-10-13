const Meta = {
  name: 'Sprints',
  icon: 'stop_circle',
  module: 'sprints',
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
    work_id: null,
    name: null,
    goals: null,
    status: 'OPEN',
    start_date: null,
    end_date: null,
    actual_start_date: null,
    actual_end_date: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
