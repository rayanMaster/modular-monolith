<?php
declare(strict_types=1);

namespace App\Enums;

enum ChartOfAccountNamesEnum: string
{
    case ASSETS = 'assets';
    case LIABILITIES = 'liabilities';
    case EXPENSES = 'expenses';
    case REVENUES = 'revenues';
    case CLIENTS = 'clients';
    case INVENTORY = 'inventory';
    case CASH = 'cash';
    case PPE = 'ppe';
    case WORKSITE_EXPENSE = 'worksite_expense';
    case EMPLOYEES = 'employees';
    case SUPPLIERS = 'suppliers';
    case CONTRACTORS = 'contractors';
    case CAPITAL = 'capital';
    case RETAINED_EARNING = 'retained_earning';
    case ACCOUNTS_PAYABLE = 'accounts_payable';
    case OTHER_LIABILITIES = 'other_liabilities';
    case LABOR_EXPENSES = 'labor_expenses';
    case COSTS = 'costs';
    case CURRENCY_DIFFERENCES = 'currency_differences';
    case OTHER_EXPENSES = 'other_expenses';
    case SALES = 'sales';
    case OTHER_REVENUES = 'other_revenues';
}
