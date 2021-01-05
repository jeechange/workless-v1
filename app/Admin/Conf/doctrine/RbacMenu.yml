Admin\Entity\RbacMenu:
  type: entity
  table: rbac_menu
  id:
    id:
      type: integer
      nullable: false
      unsigned: false
      comment: ''
      id: true
      generator:
        strategy: IDENTITY
  fields:
    pid:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
    sid:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
    names:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    module:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    nodeIds:
      type: string
      nullable: true
      length: 2048
      fixed: false
      comment: ''
      column: node_ids
    menuIds:
      type: string
      nullable: true
      length: 2048
      fixed: false
      comment: ''
      column: menu_ids
    visible:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
    defaultStatus:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
      column: default_status
    sort:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
    status:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
  lifecycleCallbacks: {  }
