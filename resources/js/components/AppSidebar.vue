<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type Auth } from '@/types';
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

const hasAnyPermission = (...permissions: string[]): boolean => {
    return permissions.some(permission => hasPermission(permission));
};

const mainNavItems = computed(() => {
    return [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
            show: true,
        },
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

const footerNavItems = computed(() => {
    return [
        {
            title: 'Settings',
            icon: BookOpen,
            href: '#',
            show: hasAnyPermission('settings.general', 'settings.mail', 'settings.sms', 'settings.payment'),
            childItems: [
                {
                    title: 'General Settings',
                    href: '/settings/general',
                    icon: Lock,
                    show: hasPermission('settings.general'),
                },
                {
                    title: 'Mail Settings',
                    href: '/settings/mail',
                    icon: Lock,
                    show: hasPermission('settings.mail'),
                },
                {
                    title: 'SMS Settings',
                    href: '/settings/sms',
                    icon: Lock,
                    show: hasPermission('settings.sms'),
                },
                {
                    title: 'Payment Settings',
                    href: '/settings/payment',
                    icon: Lock,
                    show: hasPermission('settings.payment'),
                },
            ],
        },
    ].filter(item => item.show);
});
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
            <NavMain title="Main" :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter title="" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
