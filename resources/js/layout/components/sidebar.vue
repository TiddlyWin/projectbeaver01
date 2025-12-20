<script>
import { defineComponent } from 'vue'
import dbmLogo from "../../../assets/images/dbmLogo.png";

export default defineComponent({
    computed: {
        dbmLogo() {
            return dbmLogo;
        }
    },
    data() {
        return {
            isOpen: true,
            menus: [
                { id: 1, name: "Home",     route: "/",         icon: "fa-solid fa-house" },
                { id: 2, name: "Profile",  route: "/profile",  icon: "fa-solid fa-user" },
                { id: 3, name: "Messages", route: "/messages", icon: "fa-solid fa-comments" },
                { id: 4, name: "Settings", route: "/settings", icon: "fa-solid fa-gears" },
                { id: 5, name: "Help",     route: "/help",     icon: "fa-solid fa-circle-question" },
                { id: 6, name: "Sign Out", route: "/logout",   icon: "fa-solid fa-right-from-bracket" },
            ]
        };
    }
});
</script>

<template>

    <div class="sidebar" :class="{ 'is-open': isOpen }">
        <nav class="navbar sidebar-nav">
            <div class="navbar-brand">
                <img :src="dbmLogo" class="image is-48x48 is-align-self-center ml-2" alt="DBM Logo" />
                <Transition name="fade">
                    <span v-if="isOpen" class="navbar-item title is-5">DBM Tools</span>
                </Transition>
            </div>
        </nav>

        <ul>
            <li class="sidebar-item sidebar-toggle" @click="isOpen = !isOpen">
                <a href="#" @click.prevent>
          <span class="sidebar-icon">
            <i :class="isOpen ? 'fa-solid fa-angles-left' : 'fa-solid fa-angles-right'"></i>
          </span>
                    <span class="sidebar-label">App</span>
                </a>
            </li>

            <li
                v-for="menu in menus"
                :key="menu.id"
                class="sidebar-item"
                :class="{ 'is-active': $route.path === menu.route }"
            >
                <b class="corner corner-top"></b>
                <b class="corner corner-bottom"></b>
                <router-link :to="menu.route">
                    <span class="sidebar-icon"><i :class="menu.icon"></i></span>
                    <span class="sidebar-label">{{ menu.name }}</span>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<style lang="scss">
$nav-closed: 68px;
$nav-open: 260px;
$sidebar-bg: var(--bulma-scheme-main);
$sidebar-active-bg: var(--bulma-navbar-background-color);
$corner-radius: 16px;

.sidebar-nav {
    height: 68px;
    width: $nav-open;
    background: $sidebar-bg;
    transition: width 0.35s cubic-bezier(.4, 0, .2, 1);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: $nav-closed;
    background: $sidebar-bg;
    overflow-x: hidden;
    transition: width 0.35s cubic-bezier(.4, 0, .2, 1);

    &.is-open {
        width: $nav-open;
    }

    ul {
        inset: 0;
        width: $nav-open;
        padding-top: 8px;
        list-style: none;
    }

    .sidebar-item {
        position: relative;

        a, &.sidebar-toggle > a {
            display: flex;
            align-items: center;
            height: 52px;
            text-decoration: none;
            color: var(--bulma-navbar-item-color);
            transition: color 0.15s;

            &:hover {
                color: var(--bulma-text);
                background: var(--bulma-navbar-item-hover-background-color);
            }
        }

        &.is-active {
            background: $sidebar-active-bg;

            > a,
            > .router-link-active {
                color: var(--bulma-text);
            }

            .corner {
                display: block;
            }
        }

        // Curved corner decorations
        .corner {
            display: none;
            position: absolute;
            right: 0;
            width: $corner-radius;
            height: $corner-radius;
            background: $sidebar-active-bg;

            &::before {
                content: '';
                position: absolute;
                inset: 0;
                background: $sidebar-bg;
            }

            &.corner-top {
                top: -$corner-radius;

                &::before {
                    border-bottom-right-radius: $corner-radius;
                }
            }

            &.corner-bottom {
                bottom: -$corner-radius;

                &::before {
                    border-top-right-radius: $corner-radius;
                }
            }
        }
    }

    .sidebar-toggle {
        border-bottom: 1px solid var(--bulma-border);
        margin-bottom: 4px;

        > a {
            color: var(--bulma-text-weak) !important;

            &:hover {
                background: none !important;
                color: var(--bulma-text-light) !important;
            }
        }
    }

    .sidebar-icon {
        min-width: $nav-closed;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        i {
            font-size: 1.25em;
        }
    }

    .sidebar-label {
        font-size: 0.9rem;
        white-space: nowrap;
        padding-left: 4px;
    }
}
</style>
