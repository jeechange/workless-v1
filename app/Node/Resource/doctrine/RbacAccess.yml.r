Admin\Entity\RbacAccess:
  type: entity
  table: rbac_access
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
    module:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    roleId:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
      column: role_id
    menuIds:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
      column: menu_ids
  lifecycleCallbacks: {  }
