import { useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Loader2 } from 'lucide-react';
import { Button } from '../../components/ui/button';
import { Textarea } from '../../components/ui/textarea';
import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel, FieldSet } from '../../components/ui/field';
import { ActionError } from './ActionError';
import StarRating from './StarRating';
import { reviewSchema, type ReviewFormValues } from '../shemas/review.shema';
import { ActionSuccess } from './ActionSuccess';

type Props = {
    reviewUrl: string;
};
export const ReviewForm = ({ reviewUrl }: Props) => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);

    const form = useForm<ReviewFormValues>({
        resolver: zodResolver(reviewSchema),
        defaultValues: {
            rating: 0,
            description: ''
        }
    });

    const handleSubmit = async (data: ReviewFormValues) => {
        setLoading(true);
        setError(null);

        try {
            const response = await fetch(reviewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('Unable to send your review.');
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error('Unable to send your review.');
            }

            form.reset();
            setSuccess('Your review has been sent.');
        } catch (e) {
            setError((e as Error).message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <ActionError error={error || form.formState.errors.root?.message} />
            <ActionSuccess message={success} />

            <form onSubmit={form.handleSubmit(handleSubmit)} noValidate>
                <FieldSet>
                    <FieldGroup>
                        {/* RATING */}
                        <Field>
                            <FieldLabel htmlFor="rating">Rating</FieldLabel>
                            <FieldDescription>Review our module.</FieldDescription>

                            <Controller
                                control={form.control}
                                name="rating"
                                render={({ field }) => <StarRating value={field.value} onChange={field.onChange} />}
                            />

                            {form.formState.errors.rating && (
                                <FieldError>{form.formState.errors.rating.message}</FieldError>
                            )}
                        </Field>

                        {/* COMMENT */}
                        <Field>
                            <FieldLabel htmlFor="description">Comment</FieldLabel>
                            <FieldDescription>Describe your experience</FieldDescription>

                            <Textarea
                                {...form.register('description')}
                                id="description"
                                className="bg-white"
                                placeholder="Description"
                                aria-invalid={!!form.formState.errors.description}
                            />

                            {form.formState.errors.description && (
                                <FieldError>{form.formState.errors.description.message}</FieldError>
                            )}
                        </Field>

                        {/* SUBMIT */}
                        <Field orientation="horizontal">
                            <Button type="submit" disabled={loading}>
                                {loading ? (
                                    <>
                                        <Loader2 className="animate-spin" />
                                        Enregistrement en cours...
                                    </>
                                ) : (
                                    <>Submit</>
                                )}
                            </Button>
                        </Field>
                    </FieldGroup>
                </FieldSet>
            </form>
        </>
    );
};
