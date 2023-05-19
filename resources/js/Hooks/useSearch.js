import { Inertia } from '@inertiajs/inertia';
import { debounce } from 'lodash';
import { useEffect, useRef, useState } from 'react';

export default function useSearch(searchQuery, routePath) {
  const searchInputEl = useRef(null);
  const [search, setSearch] = useState('');

  const debouncedSearch = useRef(
    debounce(async (criteria) => {
      Inertia.get(route(routePath), { search: criteria });
    }, 1000)
  ).current;

  const handleSearch = (e) => {
    setSearch(e.target.value);

    debouncedSearch(e.target.value);
  };

  useEffect(() => {
    if (searchQuery != null) {
      searchInputEl.current.focus();
    }
    setSearch(searchQuery ?? '');
  }, [searchInputEl]);

  return [search, searchInputEl, handleSearch];
}
