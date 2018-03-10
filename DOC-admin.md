# Admin extension ([nepttune/admin](https://github.com/nepttune/admin))

## Presenters

## Layouts

## Components

### ConfigMenu

> Loaded by default in `Admin` extension.

Config Menu is simple component for generating static menu. It is designed to be used as static menu in admininstration layout, but can be used anywhere else. 

Component takes an array as constructor parameter. Array has to have following format.
```
Menu:
    order:
        name: 'Order'
        icon: 'comments'
        dest: 'Order:default'
        role: 'administrator'
Menu2:
    settings:
        name: 'Settings'
        icon: 'cog'
        dest:
            category:
                name: 'Category'
                dest: 'Category:default'
            ingredient:
                name: 'Ingredient'
                dest: 'Ingredient:default'
```
Which renders as following HTML.
```
<ul class="sidebar-menu" data-widget="tree"> 
  <li class="header">Menu</li> 
  <li>
    <a href="/order/">
      <i class="fa fa-comments"></i> 
      <span>Order</span>
    </a> 
  </li> 
  <li class="header">Menu2</li> 
  <li class="treeview">
    <a href="#">
      <i class="fa fa-cog"></i> 
      <span>Settings</span> 
      <span class="pull-right-container"> 
        <i class="fa fa-angle-left pull-right"></i> 
      </span> 
    </a> 
    <ul class="treeview-menu"> 
      <li>
        <a href="/category/">Category</a>
      </li>
      <li>
        <a href="/ingredient/">Ingredient</a>
      </li> 
    </ul> 
  </li> 
</ul>
```
- `Menu` and `Menu2` are non clickable items. Can be used as headers or for multiple menu sections.
- `order` and `settings` are representation of menu items with following options.
  - `dest` - Link destination. Can be array, to create expandable sub-menu.
  - `icon` - Displayed FA icon.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. When `dest` is an array, class is added to every link. (OPTIONAL)
- `category` and `ingredient` are representation of submenu options with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. (OPTIONAL)

#### Recommended usage

```
services:
    menu:
        class: Peldax\NetteInit\Component\ConfigMenu(%configMenu%)
parameters:
    configMenu:
        Menu:
            order:
                name: 'Order'
                icon: 'comments'
                dest: 'Order:default'
                role: 'user'
```

### Breadcrumb

> Loaded by default in `Admin` extension.

This component is used to generate breadcrumbs in admin environment, but can be used anywhere else. It is simple generator which considers current module, presenter and action.

### Login Form

> Loaded by default in `Admin` extension.

### User List & Form

> Loaded by default in `Admin` extension.

## Models
