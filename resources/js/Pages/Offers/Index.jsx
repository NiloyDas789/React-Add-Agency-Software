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
import { round } from 'lodash';
import CsvDownload from '@/Components/Global/CsvDownload';
import TableFooter from '@/Components/Global/TableFooter';
import useSearch from '@/Hooks/useSearch';
import Search from '@/Components/Global/Search';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  offers,
  search: searchQuery,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = offers;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const routeName = 'offers.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('offers.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Offers deleted successfully.');
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
  const [search, searchInputEl, handleSearch] = useSearch(searchQuery, 'offers.index');

  return (
    <Authenticated auth={auth}>
      <div className="lg:flex justify-between">
        <div className="flex gap-4">
          <Link href={route('offers.create')}>
            <Button className="mb-4" icon={<PlusIcon />}>
              Add Offer
            </Button>
          </Link>

          <CsvDownload href={route('offers.export', { page, perPage })}>CSV Download</CsvDownload>

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
                orderByData={orderByData == 'provider_name' ? orderByData : null}
                orderBy="provider_name"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Provider
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
                orderByData={orderByData == 'creative' ? orderByData : null}
                orderBy="creative"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Creative
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">Length</Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'billable_payout' ? orderByData : null}
                orderBy="billable_payout"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Billable Payout
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'media_payout' ? orderByData : null}
                orderBy="media_payout"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Media Payout
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'margin' ? orderByData : null}
                orderBy="margin"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Margin
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">Qualifications</Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'dispositions' ? orderByData : null}
                orderBy="dispositions"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Disposition
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'billable_call_duration' ? orderByData : null}
                orderBy="billable_call_duration"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Billable Call Duration
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'de_dupe' ? orderByData : null}
                orderBy="de_dupe"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                De_dupe
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">Restricted States</Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'start_at' ? orderByData : null}
                orderBy="start_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Start At
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[140px]">
              <OrderByButton
                orderByData={orderByData == 'end_at' ? orderByData : null}
                orderBy="end_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                End At
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((offer) => (
            <tr key={offer.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(offer.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, offer.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('offers.edit', offer.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td>{offer.client_name}</Table.Td>
              <Table.Td>{offer.provider_name}</Table.Td>
              <Table.Td>{offer.offer}</Table.Td>
              <Table.Td>{offer.creative}</Table.Td>
              <Table.Td>
                {offer.offer_lengths.map((offer_length) => offer_length.length).join(', ')}
              </Table.Td>
              <Table.Td>{offer.billable_payout && `$${offer.billable_payout}`}</Table.Td>
              <Table.Td>{offer.media_payout && `$${offer.media_payout}`}</Table.Td>
              <Table.Td>
                {offer.billable_payout - offer.media_payout == 0
                  ? '0%'
                  : offer.billable_payout != null
                  ? `${round(
                      ((offer.billable_payout - offer.media_payout) / offer.billable_payout) * 100
                    )}%`
                  : ''}
              </Table.Td>

              <Table.Td>
                {offer.qualifications.map((qualification) => qualification.title).join(', ')}
              </Table.Td>
              <Table.Td>{offer.dispositions}</Table.Td>
              <Table.Td>{`${
                offer.billable_call_duration ? offer.billable_call_duration + ' sec' : ''
              }`}</Table.Td>
              <Table.Td>{offer.de_dupe}</Table.Td>
              <Table.Td>{offer.states.map((state) => state.name).join(', ')}</Table.Td>
              <Table.Td>
                {offer.start_at && new Date(offer.start_at).toLocaleDateString('en-us')}
              </Table.Td>
              <Table.Td>
                {offer.end_at && new Date(offer.end_at).toLocaleDateString('en-us')}
              </Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter links={links} perPage={perPage} page={page} routeName={'offers.index'} />
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
