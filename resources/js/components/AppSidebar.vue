<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Get permissions from the page props
const permissions = computed<string[]>(() => usePage().props.permissions as string[]);

// Check if the user has a specific permission
const hasPermission = (permission: string) => {
    return permissions.value.includes(permission);
};

// Define main navigation items
const mainNavItems = computed(() => {
    return [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
            show: true, // Always show
        },
        {
            title: 'Roles',
            href: '/roles',
            icon: LayoutGrid,
            show: hasPermission('Read Role'), // Show only if the user has permission
        },
        {
            title: 'Permissions',
            href: '/permissions',
            icon: LayoutGrid,
            show: hasPermission('Read Permission'),
        },
        {
            title: 'Roles & Permissions',
            href: '/roles-permissions',
            icon: LayoutGrid,
            show: hasPermission('Read Role Permission'),
        },
        {
            title: 'Users',
            href: '/users',
            icon: LayoutGrid,
            show: hasPermission('Read User'),
        },
    ].filter(item => item.show); // Filter out items that should not be shown
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
