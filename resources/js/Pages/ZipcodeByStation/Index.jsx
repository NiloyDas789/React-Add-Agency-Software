import DeleteIcon from '@/Components/Icons/DeleteIcon';
import EditIcon from '@/Components/Icons/EditIcon';
import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import { useState } from 'react';
import Create from '@/Pages/ZipcodeByStation/Create';
import { Link } from '@inertiajs/inertia-react';
import toast from 'react-hot-toast';
import DeleteModal from '@/Components/Global/DeleteModal';
import Checkbox from '@/Components/Global/Checkbox';
import useMultiSelect from '@/Hooks/useMultiSelect';
import { Inertia } from '@inertiajs/inertia';
import FileImport from './FileImport';
import TableFooter from '@/Components/Global/TableFooter';
import OrderByButton from '@/Components/Global/OrderByButton';
import useSearch from '@/Hooks/useSearch';
import Search from '@/Components/Global/Search';
import Button from '@/Components/Global/Button';

export default function Index({
  auth,
  search: searchQuery,
  zipcodeByStations,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = zipcodeByStations;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();

  const [search, searchInputEl, handleSearch] = useSearch(searchQuery, 'zipcodeByStations.index');

  const routeName = 'zipcodeByStations.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('zipcode_by_station.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Row deleted successfully!');
        setSelectedItems({ ids: [] });
      },
    });
  };

  const handleSelectAll = (e) => {
    selectAllCheckbox(
      e,
      data.map((item) => item.id)
    );
  };

  return (
    <Authenticated auth={auth}>
      <div className="flex justify-between">
        <div className="flex gap-4 items-center">
          <Create />
          {/* {data.length < 1 ? <FileImport processing={processing} /> : ''} */}
          <FileImport processing={processing} />

          {selectedItems.ids.length > 0 && (
            <span className="flex space-x-2 items-center mb-4">
              <span> {selectedItems.ids.length} Selected</span>
              <DeleteIcon onClick={() => setIsOpenModal(true)} />
            </span>
          )}
        </div>
        <Search searchInputEl={searchInputEl} handleSearch={handleSearch} search={search} />
      </div>
      <Table>
        <thead>
          <tr className="bg-slate-200">
            <Table.Th className="w-10">
              <Checkbox handleChange={handleSelectAll} checked={isAllChecked(data.length)} />
            </Table.Th>
            <Table.Th className="w-24">Actions</Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'state' ? orderByData : null}
                orderBy="state"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                State
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'area_code' ? orderByData : null}
                orderBy="area_code"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Area Code
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'zip_code' ? orderByData : null}
                orderBy="zip_code"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Zip Code
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((zipcodeByStation) => (
            <tr key={zipcodeByStation.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(zipcodeByStation.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, zipcodeByStation.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('zipcodeByStations.edit', zipcodeByStation.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{zipcodeByStation.state}</Table.Td>
              <Table.Td>{zipcodeByStation.area_code}</Table.Td>
              <Table.Td>{zipcodeByStation.zip_code}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter
          links={links}
          perPage={perPage}
          page={page}
          routeName={'zipcodeByStations.index'}
        />
      )}

      {data.length === 0 && <div className="p-4 text-center">No data found.</div>}

      <DeleteModal
        isOpenModal={isOpenModal}
        setIsOpenModal={setIsOpenModal}
        handleDelete={selectedItemsDelete}
        processing={processing}
      />
    </Authenticated>
  );
}
