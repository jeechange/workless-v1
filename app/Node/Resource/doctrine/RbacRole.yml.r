Admin\Entity\RbacRole:
  type: entity
  table: rbac_role
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
    roleName:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
      column: role_name
    status:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
    weight:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
    sort:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
  lifecycleCallbacks: {  }
