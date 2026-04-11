'use client';

import { useMemo, useState, type ReactNode } from 'react';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-react';

import TextInput from '@/components/ui/molecules/text-input';
import Typography from '@/components/ui/atoms/typography';
import cn from 'classnames';

export type SortDir = 'asc' | 'desc';

type SortDirectionSign = 1 | -1;

function sortDirectionToSign(sortDir: SortDir): SortDirectionSign {
  return sortDir === 'asc' ? 1 : -1;
}

/** Comparaison texte pour tri croissant */
export function compareStringsAsc(left: string, right: string): number {
  return left.localeCompare(right, undefined, { sensitivity: 'base' });
}

/** Comparaison numérique pour tri croissant. */
export function compareNumbersAsc(left: number, right: number): number {
  return left - right;
}

export type DataTableColumn<Row> = {
  id: string;
  header: ReactNode;
  cell: (row: Row) => ReactNode;
  /** Si défini avec `sortable: true`, la colonne est triable via ce comparateur (ordre croissant). */
  compareAscending?: (left: Row, right: Row) => number;
  sortable?: boolean;
  /**
   * Texte pris en compte par le filtre global (sous-chaîne, insensible à la casse).
   * Plusieurs colonnes peuvent en définir une : la ligne est gardée si au moins une correspond.
   */
  getFilterText?: (row: Row) => string;
  thClassName?: string;
  tdClassName?: string;
  headerAlign?: 'start' | 'center' | 'end';
};

export type DataTableFilterConfig<Row> = {
  /** Libellé du champ de recherche (texte — aligné sur `TextInput`). */
  label?: string;
  placeholder?: string;
  /**
   * Si défini, remplace l’agrégation des `getFilterText` des colonnes.
   * La ligne est gardée si la requête est une sous-chaîne d’au moins un des textes retournés.
   */
  getRowFilterTexts?: (row: Row) => string[];
};

export type DataTableProps<Row> = {
  rows: Row[];
  columns: DataTableColumn<Row>[];
  getRowId: (row: Row) => string;
  defaultSortColumnId?: string;
  filter?: DataTableFilterConfig<Row>;
  emptyMessage?: ReactNode;
  noMatchMessage?: ReactNode;
  className?: string;
  tableClassName?: string;
};

function sortIndicator(active: boolean, sortDir: SortDir) {
  if (!active) return <ArrowUpDown className="size-3.5 shrink-0 opacity-40" aria-hidden />;
  return sortDir === 'asc' ? (
    <ArrowUp className="size-3.5 shrink-0 text-accent" aria-hidden />
  ) : (
    <ArrowDown className="size-3.5 shrink-0 text-accent" aria-hidden />
  );
}

function SortableTh<Row>({
  column,
  sortKey,
  sortDir,
  onSort,
}: {
  column: DataTableColumn<Row>;
  sortKey: string;
  sortDir: SortDir;
  onSort: (id: string) => void;
}) {
  const active = sortKey === column.id;
  const end = column.headerAlign === 'end';
  return (
    <th
      scope="col"
      className={cn('w-[1%] font-normal', column.thClassName)}
      aria-sort={active ? (sortDir === 'asc' ? 'ascending' : 'descending') : 'none'}
    >
      <button
        type="button"
        onClick={() => onSort(column.id)}
        className={cn(
          'flex w-full items-center gap-1.5 py-3 pl-4 pr-2 transition-colors hover:bg-primary/10',
          end ? 'justify-end text-right' : 'justify-start text-left'
        )}
      >
        {typeof column.header === 'string' ? (
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span">
            {column.header}
          </Typography>
        ) : (
          column.header
        )}
        {sortIndicator(active, sortDir)}
      </button>
    </th>
  );
}

export default function DataTable<Row>({
  rows,
  columns,
  getRowId,
  defaultSortColumnId,
  filter,
  emptyMessage,
  noMatchMessage,
  className,
  tableClassName,
}: DataTableProps<Row>) {
  const firstSortableId = columns.find((c) => c.sortable && c.compareAscending)?.id ?? '';
  const resolvedDefaultSortId =
    defaultSortColumnId &&
    columns.some((c) => c.id === defaultSortColumnId && c.sortable && c.compareAscending)
      ? defaultSortColumnId
      : firstSortableId;

  const [sortKey, setSortKey] = useState(resolvedDefaultSortId);
  const [sortDir, setSortDir] = useState<SortDir>('asc');
  const [filterQuery, setFilterQuery] = useState('');

  const handleSort = (columnId: string) => {
    const column = columns.find((column) => column.id === columnId);
    if (!column?.sortable || !column.compareAscending) return;
    if (columnId === sortKey) {
      setSortDir((direction: SortDir) => (direction === 'asc' ? 'desc' : 'asc'));
    } else {
      setSortKey(columnId);
      setSortDir('asc');
    }
  };

  const displayedRows = useMemo(() => {
    const query = filterQuery.trim().toLowerCase();
    let list = [...rows];

    if (query && filter) {
      list = list.filter((row) => {
        const texts =
          filter.getRowFilterTexts?.(row) ??
          columns.flatMap((column) => {
            const fragment = column.getFilterText?.(row);
            return fragment ? [fragment] : [];
          });
        return texts.some((text) => text.toLowerCase().includes(query));
      });
    }

    const sortColumn = columns.find((column) => column.id === sortKey && column.compareAscending);
    if (sortColumn?.compareAscending) {
      const directionSign = sortDirectionToSign(sortDir);
      const compare = sortColumn.compareAscending;
      list.sort((left: Row, right: Row) => directionSign * compare(left, right));
    }

    return list;
  }, [rows, columns, filterQuery, sortKey, sortDir, filter]);

  if (rows.length === 0) {
    return <>{emptyMessage ?? null}</>;
  }

  const showFilter = Boolean(filter) && rows.length > 0;

  return (
    <div className={cn('flex w-full flex-col gap-4', className)}>
      {showFilter && (
        <TextInput
          name="data-table-filter"
          label={filter?.label}
          placeholder={filter?.placeholder}
          value={filterQuery}
          onChange={(e) => setFilterQuery(e.target.value)}
          sizing="md"
          className="max-w-md"
        />
      )}

      <div className="overflow-x-auto rounded-sm border border-primary/15 bg-[rgba(26,28,46,0.45)] backdrop-blur-sm">
        <table className={cn('w-full min-w-144 border-collapse text-left', tableClassName)}>
          <thead>
            <tr className="border-b border-primary/15">
              {columns.map((column) =>
                column.sortable && column.compareAscending ? (
                  <SortableTh key={column.id} column={column} sortKey={sortKey} sortDir={sortDir} onSort={handleSort} />
                ) : (
                  <th key={column.id} scope="col" className={cn('w-[1%] font-normal', column.thClassName)}>
                    <span
                      className={cn(
                        'flex items-center py-3 pl-4 pr-2',
                        column.headerAlign ? `justify-${column.headerAlign}` : ''
                      )}
                    >
                      {typeof column.header === 'string' ? (
                        <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span">
                          {column.header}
                        </Typography>
                      ) : (
                        column.header
                      )}
                    </span>
                  </th>
                )
              )}
            </tr>
          </thead>
          <tbody>
            {displayedRows.length === 0 ? (
              <tr>
                <td colSpan={columns.length} className="px-4 py-10 text-center">
                  <Typography variant="body-sm" textColor="neutral-500">
                    {noMatchMessage}
                  </Typography>
                </td>
              </tr>
            ) : (
              displayedRows.map((row) => (
                <tr
                  key={getRowId(row)}
                  className="border-b border-primary/10 last:border-b-0 hover:bg-primary/5"
                >
                  {columns.map((column) => (
                    <td key={column.id} className={cn('px-4 py-3 align-middle', column.tdClassName)}>
                      {column.cell(row)}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
