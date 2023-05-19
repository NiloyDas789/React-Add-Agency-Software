import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';
import StationCreate from '@/Pages/Stations/Create';
import TFNCreate from '@/Pages/TFNs/Create';

export default function Edit({
  auth,
  offerTollFreeNumber,
  clients,
  offers,
  stations,
  tollFreeNumbers,
  states,
}) {
  const { data, setData, put, processing, errors, reset } = useForm({
    client_id: offerTollFreeNumber.offer.client_id || '',
    offer: offerTollFreeNumber.offer.offer || '',
    creative: offerTollFreeNumber.offer.creative || '',
    toll_free_number_id: offerTollFreeNumber.toll_free_number_id?.toString() || '',
    station_id: offerTollFreeNumber.station_id?.toString() || '',
    lead_sku: offerTollFreeNumber.lead_sku || '',
    state: offerTollFreeNumber.state || '',
    length: offerTollFreeNumber.length.toString() || '',
    master: offerTollFreeNumber.master || '',
    ad_id: offerTollFreeNumber.ad_id || '',
    source_type: offerTollFreeNumber.source_type || '',
    website: offerTollFreeNumber.website || '',
    terminating_number: offerTollFreeNumber.terminating_number || '',
    data_type: offerTollFreeNumber.data_type || '',
    assigned_at: offerTollFreeNumber.assigned_at || '',
    start_at: offerTollFreeNumber.start_at || '',
    end_at: offerTollFreeNumber.end_at || '',
    test_call_at: offerTollFreeNumber.test_call_at || '',
    updateTfn: true,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('offerTollFreeNumbers.update', offerTollFreeNumber.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Offer Toll FreeNumber Updated successfully.');
      },
      onError: () => {
        toast.error('Something went wrong!');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit TFN Assignment</Heading>

        <div className="flex flex-wrap gap-x-4">
          <StationCreate />
          <TFNCreate />
        </div>

        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          clients={clients}
          offers={offers}
          stations={stations}
          states={states}
          tollFreeNumbers={tollFreeNumbers}
          isUpdating={true}
          message={errors[0]}
        />
      </div>
    </Authenticated>
  );
}
