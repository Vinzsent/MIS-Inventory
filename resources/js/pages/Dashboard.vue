<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as inventoryIndex } from '@/routes/inventory';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import HeadingSmall from '@/components/HeadingSmall.vue';

interface InventoryItem {
    id: number;
    name: string;
    sku: string;
    quantity: number;
    price: number;
    category?: string;
    is_active: boolean;
}

interface Props {
    inventoryStats: {
        total_items: number;
        active_items: number;
        total_value: number;
        low_stock_items: number;
    };
    recentItems: InventoryItem[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <HeadingSmall
                    title="Dashboard"
                    description="Overview of your inventory system"
                />
                <Link :href="inventoryIndex().url">
                    <Button>View All Inventory</Button>
                </Link>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Items
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ inventoryStats.total_items }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            All inventory items
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Active Items
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ inventoryStats.active_items }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Currently available
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Value
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ formatCurrency(inventoryStats.total_value) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Inventory worth
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Low Stock
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ inventoryStats.low_stock_items }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Items below 10 units
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Recent Inventory Items</CardTitle>
                    <CardDescription>
                        Latest items added to your inventory
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Name
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        SKU
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Category
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Quantity
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Price
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="recentItems.length === 0"
                                    class="border-b"
                                >
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-center text-muted-foreground"
                                    >
                                        No inventory items yet.
                                        <Link
                                            :href="inventoryIndex().url"
                                            class="ml-1 text-primary hover:underline"
                                        >
                                            Add your first item
                                        </Link>
                                    </td>
                                </tr>
                                <tr
                                    v-for="item in recentItems"
                                    :key="item.id"
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-medium">
                                            {{ item.name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <code
                                            class="rounded bg-muted px-2 py-1 text-xs"
                                        >
                                            {{ item.sku }}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            v-if="item.category"
                                            variant="outline"
                                        >
                                            {{ item.category }}
                                        </Badge>
                                        <span v-else class="text-muted-foreground"
                                            >—</span
                                        >
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            :variant="
                                                item.quantity === 0
                                                    ? 'destructive'
                                                    : item.quantity < 10
                                                      ? 'secondary'
                                                      : 'default'
                                            "
                                        >
                                            {{ item.quantity }}
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ formatCurrency(item.price) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge
                                            :variant="
                                                item.is_active
                                                    ? 'default'
                                                    : 'secondary'
                                            "
                                        >
                                            {{
                                                item.is_active
                                                    ? 'Active'
                                                    : 'Inactive'
                                            }}
                                        </Badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <Link :href="inventoryIndex().url">
                            <Button variant="outline">View All Items</Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
