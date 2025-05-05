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
            href: route('dashboard'),
            icon: LayoutGrid,
            show: true,
        },
        {
            title: 'Roles And Permissions',
            href: route('roles.permissions'),
            icon: Lock,
            show: hasPermission('Read Roles And Permissions'),
        },
        {
            title: 'Users',
            href: route('users.index'),
            icon: Users,
            show: hasPermission('Read Users'),
        },
    ].filter(item => item.show);
});

const footerNavItems = computed(() => {
    return [
        {
            title: 'Setting',
            icon: BookOpen,
            href: '#',
            show: hasAnyPermission('setting.general', 'setting.mail', 'setting.sms', 'setting.payment'),
            childItems: [
                {
                    title: 'General Setting',
                    href: route('general.setting'),
                    icon: Lock,
                    show: hasPermission('setting.general'),
                },
                {
                    title: 'Mail Setting',
                    href: route('mail.setting'),
                    icon: Lock,
                    show: hasPermission('setting.mail'),
                },
                {
                    title: 'SMS Setting',
                    href: route('sms.setting'),
                    icon: Lock,
                    show: hasPermission('setting.sms'),
                },
                {
                    title: 'Payment Setting',
                    href: route('payment.setting'),
                    icon: Lock,
                    show: hasPermission('setting.payment'),
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
            <NavFooter title="App" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
