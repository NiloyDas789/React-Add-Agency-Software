import React from 'react';
import Dropdown from '@/Components/Global/Dropdown';

export default function ItemsPerPage({ perPage, page, routeName }) {
  return (
    <div className="mt-7 ml-3 mb-1">
      <Dropdown>
        <Dropdown.Trigger>
          <span className="inline-flex rounded-md cursor-pointer text-slate-600 hover:text-slate-800">
            Items Per Page {perPage}
            <svg
              className="ml-1 mt-1 mr-0.5 h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              strokeWidth={1.5}
              stroke="currentColor"
            >
              <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
          </span>
        </Dropdown.Trigger>

        <Dropdown.Content align="top" width="16">
          <ul>
            {[10, 20, 50, 100].map((perPageItem) => (
              <li key={perPageItem}>
                <div>
                  <Dropdown.Link
                    href={route(routeName, { page, perPage: perPageItem })}
                    method="get"
                    as="button"
                  >
                    {perPageItem}
                  </Dropdown.Link>
                </div>
              </li>
            ))}
          </ul>
        </Dropdown.Content>
      </Dropdown>
    </div>
  );
}
