<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar'
import { ChevronRight } from 'lucide-vue-next'
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    title: string;
    items: NavItem[];
}>();

const page = usePage<SharedData>();

const hasActiveChild = (item: NavItem) => {
    return item.childItems?.some(child => child.href.replace(/^(http|https):\/\/[^/]+/, '') === page.url) || false;
};
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>{{ title }}</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <Collapsible v-if="item.childItems && item.childItems.length" as-child :default-open="item.isActive || hasActiveChild(item)" class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title" :is-active="item.href.replace(/^(http|https):\/\/[^/]+/, '') === page.url || hasActiveChild(item)">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.childItems" :key="subItem.title">
                                    <SidebarMenuSubButton as-child :is-active="subItem.href.replace(/^(http|https):\/\/[^/]+/, '') === page.url">
                                        <Link :href="subItem.href">
                                            <component :is="subItem.icon" v-if="subItem.icon" class="w-4 h-4 mr-2" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <SidebarMenuItem v-else>
                    <SidebarMenuButton as-child :is-active="item.href.replace(/^(http|https):\/\/[^/]+/, '') === page.url" :tooltip="item.title">
                        <Link :href="item.href">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
