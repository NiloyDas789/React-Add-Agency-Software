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
import CsvDownload from '@/Components/Global/CsvDownload';
import TableFooter from '@/Components/Global/TableFooter';
import useSearch from '@/Hooks/useSearch';
import Search from '@/Components/Global/Search';
import OrderByButton from '@/Components/Global/OrderByButton';

export default function Index({
  auth,
  reports,
  search: searchQuery,
  orderBy: orderByData,
  orderByType: orderByTypeData,
}) {
  const { data, links, current_page: page, per_page: perPage } = reports;
  const [isOpenModal, setIsOpenModal] = useState(false);
  const [processing, setProcessing] = useState(false);
  const [selectedItems, setSelectedItems, selectSingleCheckbox, selectAllCheckbox, isAllChecked] =
    useMultiSelect();
  const [search, searchInputEl, handleSearch] = useSearch(searchQuery, 'reports.index');
  const routeName = 'reports.index';

  const selectedItemsDelete = () => {
    Inertia.post(route('reports.delete'), selectedItems, {
      onBefore: () => setProcessing(true),
      onFinish: () => setProcessing(false),
      onSuccess: () => {
        setIsOpenModal(false);
        toast.success('Reports deleted successfully.');
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
          <CsvDownload href={route('report.export', { page, perPage, search })}>
            CSV Download
          </CsvDownload>

          {selectedItems.ids.length > 0 && (
            <span className="flex space-x-2 items-center mb-4">
              <span className="ml-2 ">{selectedItems.ids.length} Selected</span>
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
                orderByData={orderByData == 'called_at' ? orderByData : null}
                orderBy="called_at"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Called AT
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'toll_free_number' ? orderByData : null}
                orderBy="toll_free_number"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                TFN
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={
                  orderByData == 'offer_toll_free_numbers.terminating_number' ? orderByData : null
                }
                orderBy="offer_toll_free_numbers.terminating_number"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Terminating Number
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'ani' ? orderByData : null}
                orderBy="ani"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                ANI
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'duration' ? orderByData : null}
                orderBy="duration"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Duration
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'reportDisposition' ? orderByData : null}
                orderBy="reportDisposition"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Disposition
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'clientName' ? orderByData : null}
                orderBy="clientName"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Client
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'offers.offer' ? orderByData : null}
                orderBy="offers.offer"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Offer
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'offers.creative' ? orderByData : null}
                orderBy="offers.creative"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Creative
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[180px]">
              <OrderByButton
                orderByData={orderByData == 'providerName' ? orderByData : null}
                orderBy="providerName"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Provider
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'call_status' ? orderByData : null}
                orderBy="call_status"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Call Status
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'stationTitle' ? orderByData : null}
                orderBy="stationTitle"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Station
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'offers.billable_payout' ? orderByData : null}
                orderBy="offers.billable_payout"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Billable Payout
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'offers.media_payout' ? orderByData : null}
                orderBy="offers.media_payout"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Media Payout
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'tfnState' ? orderByData : null}
                orderBy="tfnState"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                State
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
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'call_recording' ? orderByData : null}
                orderBy="call_recording"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Call Recording
              </OrderByButton>
            </Table.Th>
            <Table.Th>
              <OrderByButton
                orderByData={orderByData == 'credit' ? orderByData : null}
                orderBy="credit"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Credit
              </OrderByButton>
            </Table.Th>
            <Table.Th className="min-w-[200px]">
              <OrderByButton
                orderByData={orderByData == 'credit_reason' ? orderByData : null}
                orderBy="credit_reason"
                orderByTypeData={orderByTypeData}
                routePath={routeName}
              >
                Credit Reason
              </OrderByButton>
            </Table.Th>
          </tr>
        </thead>
        <tbody>
          {data.map((report) => (
            <tr key={report.id}>
              <Table.Td>
                <Checkbox
                  checked={selectedItems.ids.indexOf(report.id) >= 0}
                  handleChange={(e) => selectSingleCheckbox(e, report.id)}
                />
              </Table.Td>
              <Table.Td>
                <div className="flex gap-4">
                  <Link
                    href={route('reports.edit', report.id)}
                    className="flex items-center"
                    aria-label="Edit Link"
                  >
                    <EditIcon />
                  </Link>
                </div>
              </Table.Td>
              <Table.Td className="whitespace-nowrap">{report.called_at}</Table.Td>
              <Table.Td>{report.toll_free_number}</Table.Td>
              <Table.Td>
                {report.terminating_number !== null
                  ? report.terminating_number
                  : report.reportTerminatingNumber}
              </Table.Td>
              <Table.Td>{report.ani}</Table.Td>
              <Table.Td>{report.duration}</Table.Td>
              <Table.Td>{report.reportDisposition}</Table.Td>
              <Table.Td>{report.clientName}</Table.Td>
              <Table.Td>{report.offer}</Table.Td>
              <Table.Td>
                {report.creative && `${report.creative} :${report.creativeLength}`}
              </Table.Td>
              <Table.Td>{report.providerName}</Table.Td>
              <Table.Td>{report.call_status}</Table.Td>
              <Table.Td>{report.Station}</Table.Td>
              <Table.Td>
                {report.call_status == 'Billable' ? `$${report.billable_payout}` : `$${0}`}
              </Table.Td>
              <Table.Td>
                {report.call_status == 'Billable' ? `$${report.media_payout}` : `$${0}`}
              </Table.Td>
              <Table.Td>{report.stateName}</Table.Td>
              <Table.Td>{report.zip_code}</Table.Td>
              <Table.Td className="whitespace-nowrap text-ellipsis max-w-[300px] overflow-clip">
                {report.call_recording && (
                  <a href={report.call_recording} target="_blank" className="text-blue-500">
                    {report.call_recording}
                  </a>
                )}
              </Table.Td>
              <Table.Td>{report.credit == 1 ? 'Yes' : 'No'}</Table.Td>
              <Table.Td>{report.credit_reason}</Table.Td>
            </tr>
          ))}
        </tbody>
      </Table>

      {data.length !== 0 && (
        <TableFooter links={links} perPage={perPage} page={page} routeName={'reports.index'} />
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
