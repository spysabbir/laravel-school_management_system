<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { Settings, Wrench, GraduationCap, FolderTree, Banknote, ListChecks, ChartBarStacked, BookOpenCheck, DollarSign, School, Component, BookCopy, BellElectric, AppWindow, MailCheck, MessageSquareMore, CircleDollarSign, LayoutGrid, Lock, Users, UserPen, BookUser, UserSearch, UserCheck } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import { can, canAny } from '@/lib/can';

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
            show: can('Read Role And Permission'),
        },
        {
            title: 'User',
            icon: Users,
            href: '#',
            show: canAny([
                'Read Admin',
                'Read Employee',
                'Read Student',
                'Read Guardian',
            ]),
            childItems: [
                {
                    title: 'Admin',
                    href: route('admins.index'),
                    icon: UserCheck,
                    show: can('Read Admin'),
                },
                {
                    title: 'Employee',
                    href: route('employees.index'),
                    icon: UserPen,
                    show: can('Read Employee'),
                },
                {
                    title: 'Student',
                    href: route('students.index'),
                    icon: BookUser,
                    show: can('Read Student'),
                },
                {
                    title: 'Guardian',
                    href: route('guardians.index'),
                    icon: UserSearch,
                    show: can('Read Guardian'),
                },
            ],
        },
        {
            title: 'Setup',
            icon: Wrench,
            href: '#',
            show: canAny([
                'Read Class',
                'Read Shift',
                'Read Group',
                'Read Subject',
                'Read Designation',
            ]),
            childItems: [
                {
                    title: 'Class',
                    href: route('classes.index'),
                    icon: School,
                    show: can('Read Class'),
                },
                {
                    title: 'Shift',
                    href: route('shifts.index'),
                    icon: BellElectric,
                    show: can('Read Shift'),
                },
                {
                    title: 'Group',
                    href: route('groups.index'),
                    icon: Component,
                    show: can('Read Group'),
                },
                {
                    title: 'Subject',
                    href: route('subjects.index'),
                    icon: BookCopy,
                    show: can('Read Subject'),
                },
                {
                    title: 'Designation',
                    href: route('designations.index'),
                    icon: FolderTree,
                    show: can('Read Designation'),
                },
            ],
        },
        {
            title: 'Academic',
            icon: GraduationCap,
            href: '#',
            show: canAny([
                'Read Exam',
                'Read Exam Type',
                'Read Exam Schedule',
                'Read Grade',
                'Read Syllabus',
            ]),
            childItems: [
                {
                    title: 'Attendance',
                    href: route('attendances.index'),
                    icon: ListChecks,
                    show: can('Read Attendance'),
                },
                {
                    title: 'Routine',
                    href: route('routines.index'),
                    icon: BookOpenCheck,
                    show: can('Read Routine'),
                },
            ],
        },
        {
            title: 'Expense',
            icon: CircleDollarSign,
            href: '#',
            show: canAny([
                'Read Expense Category',
                'Read Expense',
            ]),
            childItems: [
                {
                    title: 'Expense Category',
                    href: route('expense-categories.index'),
                    icon: ChartBarStacked,
                    show: can('Read Expense Category'),
                },
                {
                    title: 'Expense',
                    href: route('expenses.index'),
                    icon: DollarSign,
                    show: can('Read Expense'),
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
            show: canAny([
                'Read Setting',
                'Read General Setting',
                'Read Mail Setting',
                'Read SMS Setting',
                'Read Payment Setting',
            ]),
            childItems: [
                {
                    title: 'General Setting',
                    href: route('general.setting'),
                    icon: AppWindow,
                    show: can('setting.general'),
                },
                {
                    title: 'Mail Setting',
                    href: route('mail.setting'),
                    icon: MailCheck,
                    show: can('setting.mail'),
                },
                {
                    title: 'SMS Setting',
                    href: route('sms.setting'),
                    icon: MessageSquareMore,
                    show: can('setting.sms'),
                },
                {
                    title: 'Payment Setting',
                    href: route('payment.setting'),
                    icon: Banknote,
                    show: can('setting.payment'),
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
