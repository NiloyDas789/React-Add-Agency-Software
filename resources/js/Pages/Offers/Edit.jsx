import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';
import UserCreate from '@/Pages/Users/Create';
import ProviderCreate from '@/Pages/Providers/Create';
import QualificationCreate from '@/Pages/Qualifications/Create';
import StateCreate from '@/Pages/States/Create';
import InputError from '@/Components/Global/InputError';

export default function Edit({
  auth,
  offer,
  clients,
  providers,
  qualifications,
  states,
  dispositions,
}) {
  const qualificationIds = offer.qualifications.map((qualification) => qualification.id.toString());
  const stateIds = offer.states.map((state) => state.id.toString());
  const lengths = offer.offer_lengths.map((offer_length) => offer_length.length.toString());

  const { data, setData, put, processing, errors, reset } = useForm({
    client_id: offer.client_id || '',
    provider_id: offer.provider_id || '',
    offer_id: offer.offer_lengths[0].offer_id || '',
    qualification_ids: qualificationIds || [],
    state_ids: stateIds || [],
    offer: offer.offer || '',
    creative: offer.creative || '',
    lengths: lengths || [],
    billable_payout: offer.billable_payout || '',
    media_payout: offer.media_payout || '',
    margin: offer.margin || '',
    dispositions: offer.dispositions || '',
    billable_call_duration: offer.billable_call_duration || '',
    de_dupe: offer.de_dupe || '',
    start_at: offer.start_at || '',
    end_at: offer.end_at || '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('offers.update', offer.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Offer Updated Successfully.');
      },
      onError: () => {
        toast.error('Something went wrong');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Offer</Heading>

        <div className="flex flex-wrap gap-x-4">
          <UserCreate />
          <ProviderCreate />
          <QualificationCreate />
          <StateCreate />
        </div>

        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          clients={clients}
          providers={providers}
          qualifications={qualifications}
          states={states}
          dispositions={dispositions}
          isUpdating={true}
          message={errors[0]}
        />
      </div>
    </Authenticated>
  );
}
