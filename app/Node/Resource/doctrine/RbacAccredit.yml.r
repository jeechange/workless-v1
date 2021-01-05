Admin\Entity\RbacAccredit:
  type: entity
  table: rbac_accredit
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
    roleId:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
      column: role_id
    nodeId:
      type: integer
      nullable: true
      unsigned: false
      comment: ''
      column: node_id
    enable:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
    weight:
      type: smallint
      nullable: true
      unsigned: false
      comment: ''
  lifecycleCallbacks: {  }
