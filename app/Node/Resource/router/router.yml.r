#admin#_roles_states:
    pattern: /roles/states/:id/:status
    callback: "#Admin#:Roles:states"
    method: get|post
    options:
#admin#_roles_rolesAccess:
    pattern: /roles/access
    callback: "#Admin#:Roles:accessLists"
    method: get
    options:
#admin#_roles_accessModify:
    pattern: /roles/accessModify
    callback: "#Admin#:Roles:accessModify"
    method: post
    options:
#admin#_lists:
    pattern: /lists/:con/
    callback: "#Admin#:{con}:lists"
    method: get
    options: {domain: null ,require: {con: "[a-zA-Z_]+"}  }
#admin#_del_mod:
    pattern: /del_mod/:con/:act/:id
    callback: "#Admin#:{con}:{act}"
    method: get|post|delete|put
    options: {domain: null ,require: {con: "[a-zA-Z_]+",act: delete|modify,id: \d+}  ,default: {_method: DELETE} }
#admin#_add:
    pattern: /add/:con
    callback: "#Admin#:{con}:add"
    method: get|post
    options:  {require: {con: "[a-zA-Z_]+"}}