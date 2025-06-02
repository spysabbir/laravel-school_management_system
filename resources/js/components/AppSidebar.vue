<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type Auth } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Settings, Wrench, GraduationCap, FolderTree, Banknote, ListChecks, ChartBarStacked, BookOpenCheck, DollarSign, School, Component, BookCopy, BellElectric, AppWindow, MailCheck, MessageSquareMore, CircleDollarSign, LayoutGrid, Lock, Users, UserPen, UserCog, BookUser, UserSearch, UserCheck } from 'lucide-vue-next';
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
            title: 'Role And Permission',
            href: route('roles.permissions'),
            icon: Lock,
            show: hasPermission('Read Role And Permission'),
        },
        {
            title: 'User',
            icon: Users,
            href: '#',
            show: hasAnyPermission('Read Admin', 'Read Teacher', 'Read Student', 'Read Guardian'),
            childItems: [
                {
                    title: 'Admin',
                    href: route('admins.index'),
                    icon: UserCheck,
                    show: hasPermission('Read Admin'),
                },
                {
                    title: 'Teacher',
                    href: route('teachers.index'),
                    icon: UserPen,
                    show: hasPermission('Read Teacher'),
                },
                {
                    title: 'Staff',
                    href: route('staffs.index'),
                    icon: UserCog,
                    show: hasPermission('Read Staff'),
                },
                {
                    title: 'Student',
                    href: route('students.index'),
                    icon: BookUser,
                    show: hasPermission('Read Student'),
                },
                {
                    title: 'Guardian',
                    href: route('guardians.index'),
                    icon: UserSearch,
                    show: hasPermission('Read Guardian'),
                },
            ],
        },
        {
            title: 'Setup',
            icon: Wrench,
            href: '#',
            show: hasAnyPermission('Read Class', 'Read Shift', 'Read Group', 'Read Subject'),
            childItems: [
                {
                    title: 'Class',
                    href: route('classes.index'),
                    icon: School,
                    show: hasPermission('Read Class'),
                },
                {
                    title: 'Shift',
                    href: route('shifts.index'),
                    icon: BellElectric,
                    show: hasPermission('Read Shift'),
                },
                {
                    title: 'Group',
                    href: route('groups.index'),
                    icon: Component,
                    show: hasPermission('Read Group'),
                },
                {
                    title: 'Subject',
                    href: route('subjects.index'),
                    icon: BookCopy,
                    show: hasPermission('Read Subject'),
                },
                {
                    title: 'Designation',
                    href: route('designations.index'),
                    icon: FolderTree,
                    show: hasPermission('Read Designation'),
                },
            ],
        },
        {
            title: 'Academic',
            icon: GraduationCap,
            href: '#',
            show: hasAnyPermission('Read Attendance', 'Read Routine'),
            childItems: [
                {
                    title: 'Attendance',
                    href: route('attendances.index'),
                    icon: ListChecks,
                    show: hasPermission('Read Attendance'),
                },
                {
                    title: 'Routine',
                    href: route('routines.index'),
                    icon: BookOpenCheck,
                    show: hasPermission('Read Routine'),
                },
            ],
        },
        {
            title: 'Expense',
            icon: CircleDollarSign,
            href: '#',
            show: hasAnyPermission('Read Expense Category', 'Read Expense'),
            childItems: [
                {
                    title: 'Expense Category',
                    href: route('expense-categories.index'),
                    icon: ChartBarStacked,
                    show: hasPermission('Read Expense Category'),
                },
                {
                    title: 'Expense',
                    href: route('expenses.index'),
                    icon: DollarSign,
                    show: hasPermission('Read Expense'),
                }
            ],
        },
    ].filter(item => item.show);
});

const footerNavItems = computed(() => {
    return [
        {
            title: 'Setting',
            icon: Settings,
            href: '#',
            show: hasAnyPermission('setting.general', 'setting.mail', 'setting.sms', 'setting.payment'),
            childItems: [
                {
                    title: 'General Setting',
                    href: route('general.setting'),
                    icon: AppWindow,
                    show: hasPermission('setting.general'),
                },
                {
                    title: 'Mail Setting',
                    href: route('mail.setting'),
                    icon: MailCheck,
                    show: hasPermission('setting.mail'),
                },
                {
                    title: 'SMS Setting',
                    href: route('sms.setting'),
                    icon: MessageSquareMore,
                    show: hasPermission('setting.sms'),
                },
                {
                    title: 'Payment Setting',
                    href: route('payment.setting'),
                    icon: Banknote,
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
