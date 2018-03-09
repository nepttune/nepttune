# Extra navbar ([nepttune/extra-navbar](https://github.com/nepttune/extra-navbar))

This package contains component designed for generating static Bootstrap 4 navbar.

## ConfigNavbar

Usage is similiar to ConfigMenu component from `Admin` extension.

Component takes an array as constructor parameter. Array has to have following format.
```
brand:
    name: 'Brand'
    image: '/www/images/brand.png'
    dest: 'Default:default'
settings:
    name: 'Settings'
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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">
        <img src="/www/images/brand.png" width="30" height="30" alt="Brand">
        Brand
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarDropdown"><span
        class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse collapse" id="navbarDropdown">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown-settings" data-toggle="dropdown">
                    Dropdown link
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="/category/">Category</a>
                  <a class="dropdown-item" href="/ingredient/">Ingredient</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
```
- `settings` is representation of menu item with following options.
  - `dest` - Link destination. Can be array, to create expandable sub-menu.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. When `dest` is an array, class is added to every link. (OPTIONAL)
- `category` and `ingredient` are representation of dropdown options with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. (OPTIONAL)
- If `brand` key exists, it is used as brand (header) item with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `image` - Header image.

### Recommended usage

```
services:
    menu:
        class: Peldax\NetteInit\Component\ConfigNavbar(%configNavbar%)
parameters:
    configNavbar:
        brand:
            name: 'Brand'
            dest: 'Default:default'
            image: '/www/images/brand.png'
        order:
            name: 'Order'
            dest: 'Order:default'
            role: 'user'
```

