## DAO Yaml File Specification

The purpose of this document is to define the components needed to generate an HTTP endpoint for a DAO from a `.dao.yml` file

The file must be named {DAONAME}.dao.yml and saved under `src/`. They should stored in the same nested directory structure as you would like the machinery to be generated under `fab/`.  
- `dao`
    - `name`
        - Name of the DAO
    - `table_name`
        - Name of the database table containing the data that populates the DAO
    - `identity_field`
        - Name of the database column containing the unique identifier for a given DAO
    - `properties`
        - The class properties of the DAO. Each property should have:
            - `php_type`
                - The data type as represented in PHP. Should be one of string, int, float, array, or bool.
            - `database_column_name`
                - Name of the database column containing the data that populates the class property

### Example structure of a DAO yaml file:

Filename: `User.dao.yml`
```yaml
dao:
  name: User
  table_name: mv1_user
  identity_field: user_id
  properties:
    id:
      php_type: int
      database_column_name: id
    email:
      php_type: string
      database_column_name: email
    first_name:
      php_type: string
      database_column_name: fname
    last_name:
      php_type: string
      database_column_name: lname
```
