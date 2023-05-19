import DeleteIcon from '@/Components/Icons/DeleteIcon';
import EditIcon from '@/Components/Icons/EditIcon';
import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import { useState } from 'react';
import Create from '@/Pages/Stations/Create';
import { Inertia } from '@inertiajs/inertia';
import toast from 'react-hot-toast';
import { Link } from '@inertiajs/inertia-react';
import DeleteModal from '@/Components/Global/DeleteModal';
import Checkbox from '@/Components/Global/Checkbox';
import useMultiSelect from '@/Hooks/useMultiSelect';
import TableFooter from '@/Components/Global/TableFooter';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  stations,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = stations;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const routeName = 'stations.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('stations.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Stations deleted successfully.');
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
      <div className="flex gap-4">
        <Create />

        {selectedItems.ids.length > 0 && (
          <span className="flex space-x-2 items-center mb-4">
            <span> {selectedItems.ids.length} Selected</span>
            <DeleteIcon onClick={() => setIsOpenModal(true)} />
          </span>
        )}
      </div>

      <Table>
        <thead>
          <tr className="bg-slate-200">
            <Table.Th>
              <Checkbox handleChange={handleSelectAll} checked={isAllChecked(data.length)} />
            </Table.Th>
            <Table.Th className="w-24">Actions</Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'title' ? orderByData : null}
                orderBy="title"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Title
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'status' ? orderByData : null}
                orderBy="status"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Status
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((station) => (
            <tr key={station.id}>
              <Table.Td className="w-10">
                <Checkbox
                  checked={selectedItems.ids.indexOf(station.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, station.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('stations.edit', station.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{station.title}</Table.Td>
              <Table.Td>{station.status === 1 ? 'Active' : 'Inactive'}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter links={links} perPage={perPage} page={page} routeName={'stations.index'} />
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
