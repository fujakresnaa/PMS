const Meta = {
  name: 'Works',
  icon: 'stop_circle',
  module: 'works',
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
    related: null,
    name: null,
    summary: null,
    description: '',
    priority: 'REGULAR',
    status: 'OPEN',
    labels: null,
    is_module: false,
    is_done: false,
    start_date: null,
    end_date: null,
    actual_start_date: null,
    actual_end_date: null,
    mockup_link: null,
    flow_link: null,
    progress: 0,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
