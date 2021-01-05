Admin\Entity\RbacNode:
  type: entity
  table: rbac_node
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
    module:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    controller:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    action:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
    extras:
      type: string
      nullable: true
      length: 255
      fixed: false
      comment: ''
  lifecycleCallbacks: {  }
