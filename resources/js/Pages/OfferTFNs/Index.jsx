import DeleteIcon from '@/Components/Icons/DeleteIcon';
import EditIcon from '@/Components/Icons/EditIcon';
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
import useSearch from '@/Hooks/useSearch';
import Search from '@/Components/Global/Search';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  offerTFNs,
  search: searchQuery,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = offerTFNs;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const [search, searchInputEl, handleSearch] = useSearch(
    searchQuery,
    'offerTollFreeNumbers.index'
  );
  const routeName = 'offerTollFreeNumbers.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('offers_TFN.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Offers tax file numebrs deleted successfully.');
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
      <div className="lg:flex justify-between">
        <div className="flex gap-4">
          <Link href={route('offerTollFreeNumbers.create')}>
            <Button className="mb-4" icon={<PlusIcon />}>
              TFN Assignment
            </Button>
          </Link>
          <CsvDownload href={route('tfn.export', { page, perPage })}>CSV Download</CsvDownload>

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
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'client_name' ? orderByData : null}
                orderBy="client_name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Client
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'offer' ? orderByData : null}
                orderBy="offer"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Offer
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'offer_creative' ? orderByData : null}
                orderBy="offer_creative"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Creative
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'toll_free_number' ? orderByData : null}
                orderBy="toll_free_number"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                TFN
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'lead_sku' ? orderByData : null}
                orderBy="lead_sku"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                LeadSKU
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'station_name' ? orderByData : null}
                orderBy="station_name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Station
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
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
                orderByData={orderByData == 'length' ? orderByData : null}
                orderBy="length"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Length
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'master' ? orderByData : null}
                orderBy="master"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Master
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'ad_id' ? orderByData : null}
                orderBy="ad_id"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Ad_id
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'source_type' ? orderByData : null}
                orderBy="source_type"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Source Type
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'website' ? orderByData : null}
                orderBy="website"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Website
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'terminating_number' ? orderByData : null}
                orderBy="terminating_number"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Terminating Number
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'data_type' ? orderByData : null}
                orderBy="data_type"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Data Type
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'assigned_at' ? orderByData : null}
                orderBy="assigned_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Assigned Date
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'start_at' ? orderByData : null}
                orderBy="start_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Start Date
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'end_at' ? orderByData : null}
                orderBy="end_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                End Date
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'test_call_at' ? orderByData : null}
                orderBy="test_call_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Test Date
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((offerTFN) => (
            <tr key={offerTFN.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(offerTFN.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, offerTFN.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('offerTollFreeNumbers.edit', offerTFN.id)}
                    className="flex items-center"
                    aria-label="edit link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{offerTFN.client_name}</Table.Td>
              <Table.Td>{offerTFN.offer}</Table.Td>
              <Table.Td>{offerTFN.offer_creative}</Table.Td>
              <Table.Td>{offerTFN.toll_free_number}</Table.Td>
              <Table.Td>{offerTFN.lead_sku}</Table.Td>
              <Table.Td>{offerTFN.station_name}</Table.Td>
              <Table.Td>{offerTFN.state}</Table.Td>
              <Table.Td>{offerTFN.length}</Table.Td>
              <Table.Td>{offerTFN.master}</Table.Td>
              <Table.Td>{offerTFN.ad_id}</Table.Td>
              <Table.Td>{offerTFN.source_type === 1 ? 'Exclusive' : 'Shared'}</Table.Td>
              <Table.Td>{offerTFN.website}</Table.Td>
              <Table.Td>{offerTFN.terminating_number}</Table.Td>
              <Table.Td>
                {offerTFN.data_type === 1
                  ? 'TFN'
                  : offerTFN.data_type === 2
                  ? 'WEB'
                  : 'TFN and WEB'}
              </Table.Td>
              <Table.Td>
                {offerTFN.assigned_at && new Date(offerTFN.assigned_at).toLocaleDateString('en-us')}
              </Table.Td>
              <Table.Td>
                {offerTFN.start_at && new Date(offerTFN.start_at).toLocaleDateString('en-us')}
              </Table.Td>
              <Table.Td>
                {offerTFN.end_at && new Date(offerTFN.end_at).toLocaleDateString('en-us')}
              </Table.Td>
              <Table.Td>
                {offerTFN.test_call_at &&
                  new Date(offerTFN.test_call_at).toLocaleDateString('en-us')}
              </Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter
          links={links}
          perPage={perPage}
          page={page}
          routeName={'offerTollFreeNumbers.index'}
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
