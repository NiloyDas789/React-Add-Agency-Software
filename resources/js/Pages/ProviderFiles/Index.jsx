import DeleteIcon from '@/Components/Icons/DeleteIcon';
import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import { useState } from 'react';
import { Inertia } from '@inertiajs/inertia';
import toast from 'react-hot-toast';
import { Link } from '@inertiajs/inertia-react';
import DeleteModal from '@/Components/Global/DeleteModal';
import Button from '@/Components/Global/Button';
import PlusIcon from '@/Components/Icons/PlusIcon';
import useMultiSelect from '@/Hooks/useMultiSelect';
import Checkbox from '@/Components/Global/Checkbox';
import CsvDownload from '@/Components/Global/CsvDownload';
import TableFooter from '@/Components/Global/TableFooter';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  providerFiles,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = providerFiles;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);

  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const routeName = 'provider-files.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('provider_files.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success("Provider's file deleted successfully.");
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
        <Link href={route('provider-files.create')}>
          <Button className="mb-4" icon={<PlusIcon />}>
            Import Provider File
          </Button>
        </Link>

        <CsvDownload href={route('provider_files.export', { page, perPage })}>
          CSV Download
        </CsvDownload>

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
            <Table.Th className="w-10">
              <Checkbox handleChange={handleSelectAll} checked={isAllChecked(data.length)} />
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'provider_id' ? orderByData : null}
                orderBy="provider_id"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Data Provider
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'file_name' ? orderByData : null}
                orderBy="file_name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                File Name
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'received_at' ? orderByData : null}
                orderBy="received_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Received At
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'updated_at' ? orderByData : null}
                orderBy="updated_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Last Updated At
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'status' ? orderByData : null}
                orderBy="status"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Process Status
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((providerFile) => (
            <tr key={providerFile.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(providerFile.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, providerFile.id)}
                />
              </Table.Td>
              <Table.Td>{providerFile.provider.name}</Table.Td>
              <Table.Td>
                <a
                  target="_blank"
                  href={route('provider_file.download', providerFile.id)}
                  className="hover:text-blue-500"
                >
                  {providerFile.file_name}
                </a>
              </Table.Td>
              <Table.Td className="whitespace-nowrap">{providerFile.received_at}</Table.Td>
              <Table.Td>{new Date(providerFile.updated_at).toLocaleString()}</Table.Td>
              <Table.Td>{providerFile.status}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter
          links={links}
          perPage={perPage}
          page={page}
          routeName={'provider-files.index'}
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
