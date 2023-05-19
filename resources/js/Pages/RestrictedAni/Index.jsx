import DeleteIcon from '@/Components/Icons/DeleteIcon';
import EditIcon from '@/Components/Icons/EditIcon';
import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import { useState } from 'react';
import { Inertia } from '@inertiajs/inertia';
import toast from 'react-hot-toast';
import { Link } from '@inertiajs/inertia-react';
import DeleteModal from '@/Components/Global/DeleteModal';
import useMultiSelect from '@/Hooks/useMultiSelect';
import Checkbox from '@/Components/Global/Checkbox';
import Button from '@/Components/Global/Button';
import PlusIcon from '@/Components/Icons/PlusIcon';
import TableFooter from '@/Components/Global/TableFooter';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  restrictedAnis,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = restrictedAnis;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const routeName = 'restricted-ani.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('restricted_ani.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success("Restricted Ani's deleted successfully.");
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
        <Link href={route('restricted-ani.create')}>
          <Button className="mb-4" icon={<PlusIcon />}>
            Add Restricted Ani
          </Button>
        </Link>

        {selectedItems.ids.length > 0 && (
          <span className="flex items-center mb-4">
            <DeleteIcon onClick={() => setIsOpenModal(true)} />
            <span className="ml-2 ">{selectedItems.ids.length} Selected</span>
          </span>
        )}
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
                orderByData={orderByData == 'restricted_ani' ? orderByData : null}
                orderBy="restricted_ani"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Restricted Ani
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'date' ? orderByData : null}
                orderBy="date"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Date
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'reason' ? orderByData : null}
                orderBy="reason"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Reason
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((restrictedAni) => (
            <tr key={restrictedAni.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(restrictedAni.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, restrictedAni.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('restricted-ani.edit', restrictedAni.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{restrictedAni.restricted_ani}</Table.Td>
              <Table.Td>
                {restrictedAni.date && new Date(restrictedAni.date).toLocaleDateString('en-us')}
              </Table.Td>
              <Table.Td>{restrictedAni.reason}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter
          links={links}
          perPage={perPage}
          page={page}
          routeName={'restricted-ani.index'}
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
