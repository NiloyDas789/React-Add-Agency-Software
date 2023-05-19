import React from 'react';
import ItemsPerPage from '@/Components/Global/ItemsPerPage';
import Pagination from '@/Components/Global/Pagination';

export default function PaginationFooter({ perPage, page, links, routeName }) {
  return (
    <div className="flex justify-between">
      <ItemsPerPage perPage={perPage} page={page} routeName={routeName} />
      <Pagination links={links} />
    </div>
  );
}
