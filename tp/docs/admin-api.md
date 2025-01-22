# 管理后台API文档

## 基础信息
- 基础路径: `/api/admin`
- 响应格式: JSON
- 认证方式: Bearer Token

## 通用响应格式
```json
{
    "code": 200,      // 状态码：200成功，400失败
    "message": "",    // 响应信息
    "data": null      // 响应数据
}
```

## 错误码说明
- 200: 成功
- 400: 请求参数错误
- 401: 未登录或登录已过期
- 403: 无权限访问
- 404: 资源不存在
- 500: 服务器内部错误

## 通用请求头
```
Content-Type: application/json
Authorization: Bearer {token}  // 登录后的接口需要携带
```

## 环境说明
- 开发环境：`http://dev-api.example.com`
- 测试环境：`http://test-api.example.com`
- 生产环境：`http://api.example.com`

## 登录相关接口

### 1. 获取登录验证码
- 请求路径：`/login/code`
- 请求方法：`GET`
- 请求参数(Query String)：
  ```
  username=admin  // 用户名，必填，4-20位字符
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "验证码已发送",
      "data": {
          "code": "123456"  // 验证码，6位数字
      }
  }
  ```
- 错误响应：
  ```json
  {
      "code": 400,
      "message": "用户名不能为空",
      "data": null
  }
  ```

### 2. 管理员登录
- 请求路径：`/login`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "username": "admin",  // 用户名，必填，4-20位字符
      "password": "123456", // 密码，必填，6-20位字符
      "code": "123456"     // 验证码，必填，6位数字
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "登录成功",
      "data": {
          "token": "xxxxxxxx",  // 登录令牌
          "admin": {
              "id": 1,
              "username": "admin",
              "nickname": "管理员",
              "avatar": "",
              "email": "",
              "mobile": "",
              "status": 1,
              "status_text": "启用",
              "role": {         // 角色信息
                  "id": 1,
                  "name": "超级管理员"
              },
              "create_time": "2024-01-01 00:00:00",
              "last_login_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 3. 退出登录
- 请求路径：`/logout`
- 请求方法：`POST`
- 请求头：
  ```
  Authorization: Bearer {token}
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "退出成功",
      "data": null
  }
  ```

## 注意事项
1. 验证码有效期为5分钟
2. 验证码使用一次后立即失效
3. token有效期为2小时
4. 所有请求需要添加跨域请求头
5. 登录后的请求需要在请求头中携带token

## 管理员管理

### 1. 获取管理员列表
- 请求路径：`/admins`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "status": null      // 状态筛选，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "username": "admin",
                  "nickname": "管理员",
                  "avatar": "",
                  "email": "",
                  "mobile": "",
                  "status": 1,
                  "status_text": "启用",
                  "role": {
                      "id": 1,
                      "name": "超级管理员"
                  },
                  "create_time": "2024-01-01 00:00:00",
                  "last_login_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

### 2. 创建管理员
- 请求路径：`/admins`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "username": "admin2",     // 用户名，必填，4-20位
      "password": "123456",     // 密码，必填，6-20位
      "nickname": "管理员2",    // 昵称，必填，2-20位
      "avatar": "",            // 头像URL，可选
      "email": "",            // 邮箱，可选
      "mobile": "",           // 手机号，可选
      "role_id": 2,          // 角色ID，必填
      "status": 1            // 状态，必填：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "创建成功",
      "data": {
          "admin": {
              "id": 2,
              "username": "admin2",
              "nickname": "管理员2",
              "status": 1,
              "status_text": "启用",
              "role": {
                  "id": 2,
                  "name": "编辑"
              },
              "create_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 3. 更新管理员
- 请求路径：`/admins/:id`
- 请求方法：`PUT`
- 请求参数：
  ```json
  {
      "nickname": "管理员2",    // 昵称，可选
      "avatar": "",            // 头像URL，可选
      "email": "",            // 邮箱，可选
      "mobile": "",           // 手机号，可选
      "role_id": 2,          // 角色ID，可选
      "status": 1            // 状态，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "更新成功",
      "data": {
          "admin": {
              "id": 2,
              "username": "admin2",
              "nickname": "管理员2",
              "status": 1,
              "status_text": "启用",
              "role": {
                  "id": 2,
                  "name": "编辑"
              },
              "update_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 4. 删除管理员
- 请求路径：`/admins/:id`
- 请求方法：`DELETE`
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "删除成功",
      "data": null
  }
  ```

### 5. 修改密码
- 请求路径：`/admins/password`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "old_password": "123456",  // 旧密码，必填
      "password": "654321",      // 新密码，必填，6-20位
      "confirm_password": "654321" // 确认密码，必填，需与新密码一致
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "密码修改成功",
      "data": null
  }
  ```

## 角色管理

### 1. 获取角色列表
- 请求路径：`/roles`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "status": null      // 状态筛选，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "name": "超级管理员",
                  "description": "系统最高权限",
                  "status": 1,
                  "status_text": "启用",
                  "permissions": [   // 角色拥有的权限
                      {
                          "id": 1,
                          "name": "管理员管理",
                          "path": "/admins"
                      }
                  ],
                  "create_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

### 2. 创建角色
- 请求路径：`/roles`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "name": "编辑",           // 角色名称，必填，2-20位
      "description": "内容编辑", // 描述，可选
      "status": 1,            // 状态，必填：0禁用，1启用
      "permission_ids": [1,2,3] // 权限ID数组，必填
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "创建成功",
      "data": {
          "role": {
              "id": 2,
              "name": "编辑",
              "description": "内容编辑",
              "status": 1,
              "status_text": "启用",
              "permissions": [
                  {
                      "id": 1,
                      "name": "文章管理"
                  }
              ],
              "create_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 3. 更新角色
- 请求路径：`/roles/:id`
- 请求方法：`PUT`
- 请求参数：
  ```json
  {
      "name": "编辑",           // 角色名称，可选
      "description": "内容编辑", // 描述，可选
      "status": 1,            // 状态，可选：0禁用，1启用
      "permission_ids": [1,2,3] // 权限ID数组，可选
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "更新成功",
      "data": {
          "role": {
              "id": 2,
              "name": "编辑",
              "description": "内容编辑",
              "status": 1,
              "status_text": "启用",
              "permissions": [
                  {
                      "id": 1,
                      "name": "文章管理"
                  }
              ],
              "update_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 4. 删除角色
- 请求路径：`/roles/:id`
- 请求方法：`DELETE`
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "删除成功",
      "data": null
  }
  ```

## 权限管理

### 1. 获取权限列表
- 请求路径：`/permissions`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "status": null      // 状态筛选，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "name": "管理员管理",
                  "path": "/admins",
                  "method": "GET",
                  "description": "管理员列表",
                  "status": 1,
                  "status_text": "启用",
                  "create_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

### 2. 创建权限
- 请求路径：`/permissions`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "name": "文章管理",       // 权限名称，必填，2-20位
      "path": "/articles",     // 权限路径，必填
      "method": "GET",        // 请求方法，必填：GET/POST/PUT/DELETE
      "description": "文章列表", // 描述，可选
      "status": 1            // 状态，必填：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "创建成功",
      "data": {
          "permission": {
              "id": 2,
              "name": "文章管理",
              "path": "/articles",
              "method": "GET",
              "description": "文章列表",
              "status": 1,
              "status_text": "启用",
              "create_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

## 操作日志

### 1. 获取日志列表
- 请求路径：`/logs`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "date_range": [     // 日期范围，可选
          "2024-01-01 00:00:00",
          "2024-01-31 23:59:59"
      ]
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "admin_id": 1,
                  "admin_name": "admin",
                  "path": "/api/admin/articles",
                  "method": "POST",
                  "ip": "127.0.0.1",
                  "content": "创建文章",
                  "create_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

## 文章管理

### 1. 获取文章列表
- 请求路径：`/articles`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "category_id": null, // 栏目ID，可选
      "status": null,     // 状态筛选，可选：0草稿，1发布
      "date_range": [     // 日期范围，可选
          "2024-01-01 00:00:00",
          "2024-01-31 23:59:59"
      ]
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "title": "文章标题",
                  "description": "文章描述",
                  "cover": "cover.jpg",
                  "author": "作者",
                  "category_id": 1,
                  "category_name": "新闻",
                  "views": 100,
                  "status": 1,
                  "status_text": "已发布",
                  "create_time": "2024-01-01 00:00:00",
                  "update_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

### 2. 创建文章
- 请求路径：`/articles`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "title": "文章标题",      // 标题，必填，2-100位
      "description": "描述",    // 描述，可选，最多255位
      "content": "文章内容",    // 内容，必填
      "cover": "cover.jpg",    // 封面图，可选
      "author": "作者",        // 作者，可选
      "category_id": 1,       // 栏目ID，必填
      "tags": ["标签1","标签2"], // 标签，可选
      "status": 1            // 状态，必填：0草稿，1发布
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "创建成功",
      "data": {
          "article": {
              "id": 1,
              "title": "文章标题",
              "description": "文章描述",
              "cover": "cover.jpg",
              "author": "作者",
              "category_id": 1,
              "category_name": "新闻",
              "views": 0,
              "status": 1,
              "status_text": "已发布",
              "create_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 3. 更新文章
- 请求路径：`/articles/:id`
- 请求方法：`PUT`
- 请求参数：
  ```json
  {
      "title": "文章标题",      // 标题，可选
      "description": "描述",    // 描述，可选
      "content": "文章内容",    // 内容，可选
      "cover": "cover.jpg",    // 封面图，可选
      "author": "作者",        // 作者，可选
      "category_id": 1,       // 栏目ID，可选
      "tags": ["标签1","标签2"], // 标签，可选
      "status": 1            // 状态，可选：0草稿，1发布
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "更新成功",
      "data": {
          "article": {
              "id": 1,
              "title": "文章标题",
              "description": "文章描述",
              "cover": "cover.jpg",
              "author": "作者",
              "category_id": 1,
              "category_name": "新闻",
              "views": 100,
              "status": 1,
              "status_text": "已发布",
              "update_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 4. 删除文章
- 请求路径：`/articles/:id`
- 请求方法：`DELETE`
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "删除成功",
      "data": null
  }
  ```

## 栏目管理

### 1. 获取栏目列表
- 请求路径：`/categories`
- 请求方法：`GET`
- 请求参数：
  ```json
  {
      "page": 1,          // 页码，默认1
      "pageSize": 15,     // 每页数量，默认15
      "keyword": "",      // 搜索关键词，可选
      "status": null      // 状态筛选，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "name": "新闻动态",
                  "description": "网站新闻",
                  "parent_id": 0,
                  "sort": 1,
                  "status": 1,
                  "status_text": "启用",
                  "children": [      // 子栏目
                      {
                          "id": 2,
                          "name": "公司新闻",
                          "description": "公司新闻",
                          "parent_id": 1,
                          "sort": 1,
                          "status": 1,
                          "status_text": "启用"
                      }
                  ],
                  "create_time": "2024-01-01 00:00:00"
              }
          ],
          "total": 1,
          "page": 1,
          "pageSize": 15
      }
  }
  ```

### 2. 创建栏目
- 请求路径：`/categories`
- 请求方法：`POST`
- 请求参数：
  ```json
  {
      "name": "新闻动态",      // 栏目名称，必填，2-50位
      "description": "网站新闻", // 描述，可选
      "parent_id": 0,         // 父栏目ID，必填，0为顶级栏目
      "sort": 1,             // 排序，必填，数字越小越靠前
      "status": 1            // 状态，必填：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "创建成功",
      "data": {
          "category": {
              "id": 1,
              "name": "新闻动态",
              "description": "网站新闻",
              "parent_id": 0,
              "sort": 1,
              "status": 1,
              "status_text": "启用",
              "create_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 3. 更新栏目
- 请求路径：`/categories/:id`
- 请求方法：`PUT`
- 请求参数：
  ```json
  {
      "name": "新闻动态",      // 栏目名称，可选
      "description": "网站新闻", // 描述，可选
      "parent_id": 0,         // 父栏目ID，可选
      "sort": 1,             // 排序，可选
      "status": 1            // 状态，可选：0禁用，1启用
  }
  ```
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "更新成功",
      "data": {
          "category": {
              "id": 1,
              "name": "新闻动态",
              "description": "网站新闻",
              "parent_id": 0,
              "sort": 1,
              "status": 1,
              "status_text": "启用",
              "update_time": "2024-01-01 00:00:00"
          }
      }
  }
  ```

### 4. 删除栏目
- 请求路径：`/categories/:id`
- 请求方法：`DELETE`
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "删除成功",
      "data": null
  }
  ```

### 5. 获取栏目树形结构
- 请求路径：`/categories/tree`
- 请求方法：`GET`
- 响应数据：
  ```json
  {
      "code": 200,
      "message": "success",
      "data": {
          "list": [
              {
                  "id": 1,
                  "name": "新闻动态",
                  "description": "网站新闻",
                  "parent_id": 0,
                  "sort": 1,
                  "status": 1,
                  "status_text": "启用",
                  "children": [
                      {
                          "id": 2,
                          "name": "公司新闻",
                          "description": "公司新闻",
                          "parent_id": 1,
                          "sort": 1,
                          "status": 1,
                          "status_text": "启用",
                          "children": []
                      }
                  ]
              }
          ]
      }
  }
  ``` 