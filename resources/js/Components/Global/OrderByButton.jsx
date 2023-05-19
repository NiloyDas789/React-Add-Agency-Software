import { Link } from '@inertiajs/inertia-react';
import React from 'react';
import DownArrowIcon from '../Icons/DownArrowIcon';
import UpArrowIcon from '../Icons/UpArrowIcon';

export default function OrderByButton({
  children,
  orderBy,
  routePath,
  orderByData,
  orderByTypeData,
}) {
  return (
    <>
      <Link
        href={route(routePath)}
        data={{
          orderBy: orderBy,
          orderByType: orderByTypeData == null || orderByTypeData == 'desc' ? 'asc' : 'desc',
        }}
        className="flex"
        preserveScroll
      >
        {children}
        {orderByData ? (
          orderByTypeData == null || orderByTypeData == 'desc' ? (
            <DownArrowIcon />
          ) : (
            <UpArrowIcon />
          )
        ) : null}
      </Link>
    </>
  );
}
