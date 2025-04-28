<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type Auth } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, LayoutGrid, Lock, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const auth = computed(() => usePage().props.auth as Auth);

const hasPermission = (permission: string): boolean => {
    if (auth.value.roles.includes('Super Admin')) {
        return true;
    }

    return auth.value.permissions.includes(permission);
};

// const hasAnyPermission = (...permissions: string[]): boolean => {
//     return permissions.some(permission => hasPermission(permission));
// };

const mainNavItems = computed(() => {
    return [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
            show: true,
        },
        // {
        //     title: 'User Management',
        //     icon: Lock,
        //     href: '#',
        //     show: hasAnyPermission('user.view', 'user.create', 'user.edit', 'user.delete'),
        //     childItems: [
        //     {
        //         title: 'User List',
        //         href: '/users',
        //         icon: Lock,
        //         show: hasPermission('user.view'),
        //     },
        //     {
        //         title: 'Create User',
        //         href: '/users/create',
        //         icon: Lock,
        //         show: hasPermission('user.create'),
        //     },
        //     ].filter(item => item.show),
        // },
        {
            title: 'Roles And Permissions',
            href: '/roles-permissions',
            icon: Lock,
            show: hasPermission('Read Roles And Permissions'),
        },
        {
            title: 'Users',
            href: '/users',
            icon: Users,
            show: hasPermission('Read Users'),
        },
    ].filter(item => item.show);
});

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain title="" :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
