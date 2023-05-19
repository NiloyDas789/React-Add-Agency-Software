import DeleteIcon from '@/Components/Icons/DeleteIcon';
import EditIcon from '@/Components/Icons/EditIcon';
import Table from '@/Components/Global/Table';
import Authenticated from '@/Layouts/Authenticated';
import { useState } from 'react';
import Create from '@/Pages/Providers/Create';
import { Inertia } from '@inertiajs/inertia';
import toast from 'react-hot-toast';
import { Link } from '@inertiajs/inertia-react';
import DeleteModal from '@/Components/Global/DeleteModal';
import useMultiSelect from '@/Hooks/useMultiSelect';
import Checkbox from '@/Components/Global/Checkbox';
import CsvDownload from '@/Components/Global/CsvDownload';
import TableFooter from '@/Components/Global/TableFooter';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  providers,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = providers;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const routeName = 'providers.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('providers.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Providers deleted successfully.');
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
        <Create providers={providers} />

        <CsvDownload href={route('providers.export', { page, perPage })} processing={processing}>
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
            <Table.Th className="w-24">Actions</Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'name' ? orderByData : null}
                orderBy="name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Name
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'delivery_method' ? orderByData : null}
                orderBy="delivery_method"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Delivery Method
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'response_type' ? orderByData : null}
                orderBy="response_type"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Response Type
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'timezone' ? orderByData : null}
                orderBy="timezone"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Timezone
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'delivery_days' ? orderByData : null}
                orderBy="delivery_days"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Delivery Days
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'auto_delivery' ? orderByData : null}
                orderBy="auto_delivery"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Auto Delivery
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'file_naming_convention' ? orderByData : null}
                orderBy="file_naming_convention"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                File Naming Convention
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'contact_name' ? orderByData : null}
                orderBy="contact_name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Contact Name
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'contact_email' ? orderByData : null}
                orderBy="contact_email"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Contact Email
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((provider) => (
            <tr key={provider.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(provider.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, provider.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('providers.edit', provider.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{provider.name}</Table.Td>
              <Table.Td>{provider.delivery_method}</Table.Td>
              <Table.Td>{provider.response_type}</Table.Td>
              <Table.Td>{provider.timezone}</Table.Td>
              <Table.Td>{provider.delivery_days}</Table.Td>
              <Table.Td>{provider.auto_delivery}</Table.Td>
              <Table.Td>{provider.file_naming_convention}</Table.Td>
              <Table.Td>{provider.contact_name}</Table.Td>
              <Table.Td>{provider.contact_email}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter links={links} perPage={perPage} page={page} routeName={'providers.index'} />
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
